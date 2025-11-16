<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactCustomField;
use App\Models\ContactEmail;
use App\Models\ContactPhone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{


    public function contactList(Request $request)
    {
        // Get the search value from the request
        $search = $request->input('search');
        $perPage = (int) ($request->query('per_page', 10));

        if ($search) {

            $contacts = Contact::query()
                ->when($search, function (Builder $query, string $search) {
                    $query->where(function (Builder $subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            // Add a search condition for the custom fields
                            ->orWhereHas('custom_field', function (Builder $q) use ($search) {
                                // Assuming the custom field's value is in a 'field_value' column
                                $q->where('value', 'like', "%{$search}%");
                            });
                    });
                })
                ->paginate($perPage);
        } else {

            $contacts = Contact::with(['custom_field', 'mergedPhones', 'mergedEmails'])->orderBy('id', 'desc')->paginate($perPage);
        }

        return response()->json($contacts); // includes data, current_page, last_page, total, etc. [web:85]
    }

    public function destroy(Contact $contact)
    {
        try {
            // The contact's related data (like custom fields) should be deleted
            // automatically if you have set up cascading deletes in your database migration.
            // Otherwise, you must delete them manually here first.

            $contact->delete();
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("Failed to delete contact #{$contact->id}: {$e->getMessage()}");

            // Return a server error response
            return response()->json(['message' => 'An error occurred while deleting the contact.'], 500);
        }

        // Return a success response
        return response()->json(['message' => 'Contact deleted successfully.']);
    }

    public function createContact(Request $request)
    {
        //   dd($request->all());
        // Validate request
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255'],
            'phone'           => ['required', 'string', 'max:30'],
            'gender'          => ['required', 'in:male,female,other'],
        ]);
        $customeFields = json_decode($request->custom_fields, true);
        $profileImagePath = $request->file('profile_image')->store('contacts/profile_images', 'public'); // [web:29][web:21]
        $additionalFilePath = $request->hasFile('additional_file')
            ? $request->file('additional_file')->store('contacts/additional_files', 'public')
            : null; // [web:29][web:21]


        DB::beginTransaction();

        try {
            // If saving to DB (example)
            $contact =  Contact::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
                'profile_image_path' => $profileImagePath,
                'additional_file_path' => $additionalFilePath,
            ]); // Use fillable columns on Contact model [web:22][web:29]


            ContactEmail::create([
                "contact_id" => $contact->id,
                "email" => $validated['email'],
                "is_primary" => true,
            ]);

            ContactPhone::create([
                "contact_id" => $contact->id,
                "phone" => $validated['phone'],
                "is_primary" => true,
            ]);

            if ($customeFields) {
                foreach ($customeFields as $cf) {
                    ContactCustomField::create([
                        "contact_id" => $contact->id,
                        'label' => $cf['label'],
                        'type' => $cf['type'],
                        'value' => $cf['value'],
                        'options' => json_encode($cf['options']),
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return response()->json([
            'message' => 'Contact created successfully.',
            'data' => [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
                'profile_image_url' => Storage::disk('public')->url($profileImagePath),
                'additional_file_url' => $additionalFilePath ? Storage::disk('public')->url($additionalFilePath) : null,
            ],
        ], 201);
    }


    public function update(Request $request, Contact $contact)
    {

       
        // Validate request data
        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'email', 'max:255', Rule::unique('contacts')->ignore($contact->id)],
            'phone'  => ['required', 'string', 'max:30'],
            'gender' => ['required', 'in:male,female,other'],
        ]);

        $customFields = json_decode($request->custom_fields, true);
//  dd($customFields);
        DB::beginTransaction();

        try {
            // --- Handle File Uploads ---

            $profileImagePath = $contact->profile_image_path;
            if ($request->hasFile('profile_image')) {
                // Delete the old image if it exists
                if ($contact->profile_image_path) {
                    Storage::disk('public')->delete($contact->profile_image_path);
                }
                // Store the new image
                $profileImagePath = $request->file('profile_image')->store('contacts/profile_images', 'public');
            }

            $additionalFilePath = $contact->additional_file_path;
            if ($request->hasFile('additional_file')) {
                if ($contact->additional_file_path) {
                    Storage::disk('public')->delete($contact->additional_file_path);
                }
                $additionalFilePath = $request->file('additional_file')->store('contacts/additional_files', 'public');
            }

            // --- Update Main Contact Record ---
            $contact->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
                'profile_image_path' => $profileImagePath,
                'additional_file_path' => $additionalFilePath,
            ]);

            // --- Update Primary Email and Phone ---
            // This ensures the separate tables are also in sync
            $contact->mergedEmails()->where('is_primary', true)->update(['email' => $validated['email']]);
            $contact->mergedPhones()->where('is_primary', true)->update(['phone' => $validated['phone']]);

            // --- Sync Custom Fields ---
            // A simple and robust way to handle updates is to delete and recreate.
            $contact->custom_field()->delete();

            // dd($customFields);
            if ($customFields) {
                foreach ($customFields as $cf) {
                    ContactCustomField::create([
                        "contact_id" => $contact->id,
                        'label' => $cf['label'],
                        'type' => $cf['type'],
                        'value' => $cf['value'],
                        // Ensure options are stored as JSON
                        'options' => isset($cf['options']) ? json_encode($cf['options']) : null,
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Failed to update contact #{$contact->id}: {$e->getMessage()}");
            // Return a JSON error response
            return response()->json(['message' => 'An error occurred during the update.'], 500);
        }

        // Return a success JSON response with the updated data
        return response()->json([
            'message' => 'Contact updated successfully.',
            'data' => $contact->fresh()->load('custom_field'), // Reload the model to get the latest data
        ], 200);
    }


    public function searchForMerge(Request $request)
    {
        $search = $request->input('query');
        $excludeId = $request->input('exclude_id');

        $contacts = Contact::query()
            ->where('id', '!=', $excludeId) // Exclude the contact being merged
            ->where(function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->limit(10) // Limit the number of results for performance
            ->get(['id', 'name', 'phone', 'email']);

        return response()->json($contacts);
    }

    public function merge(Request $request)
    {
        $validated = $request->validate([
            'source_contact_id' => 'required|integer|exists:contacts,id',
            'destination_contact_id' => 'required|integer|exists:contacts,id|different:source_contact_id',
        ]);

        $sourceContactId = $validated['source_contact_id'];
        $destinationContactId = $validated['destination_contact_id'];

        try {
            DB::transaction(function () use ($sourceContactId, $destinationContactId) {
                $sourceContact = Contact::findOrFail($sourceContactId);
                $destinationContact = Contact::findOrFail($destinationContactId);

                $sourceContact->merged_into_id = $destinationContact->id;
                $sourceContact->status = 'merged';
                $sourceContact->merged_at = now();

                $sourceContact->save();


                ContactCustomField::where('contact_id', $sourceContact->id)
                    ->update(['contact_id' => $destinationContact->id]);
                // dd($destinationContact);

                if ($sourceContact->email !=  $destinationContact->email) {

                    ContactEmail::where('contact_id', $sourceContact->id)
                        ->update(['contact_id' => $destinationContact->id, "is_primary" => 0]);
                }

                if ($sourceContact->phone !=  $destinationContact->phone) {

                    ContactPhone::where('contact_id', $sourceContact->id)
                        ->update(['contact_id' => $destinationContact->id, "is_primary" => 0]);
                }
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred during the merge process. Please try again.'], 500);
        }
        return response()->json(['message' => 'Contacts merged successfully.']);
    }
}
