<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - CRUD Application</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: 600;
        }

        .stats-card {
            border-radius: 10px;
            transition: transform 0.2s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .table-responsive {
            border-radius: 8px;
        }

        .table thead {
            background-color: #f8f9fa;
        }

        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            margin: 0 2px;
        }

        .status-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .search-box {
            max-width: 300px;
        }

        .modal-header {
            background-color: #f8f9fa;
        }

        .toast-container {
            z-index: 9999;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .sortable {
            cursor: pointer;
            user-select: none;
        }

        .sortable:hover {
            background-color: #e9ecef;
        }

        .pagination {
            margin-top: 1rem;
        }
    </style>
</head>

<body>

    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="toastNotification" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi me-2" id="toastIcon"></i>
                <strong class="me-auto" id="toastTitle">Notification</strong>
                <small>Just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                Message here
            </div>
        </div>
    </div>


    <div class="page-header">
        <div class="container">
            <h1 class="mb-0">User Management System</h1>
        </div>
    </div>

    <div class="container mb-5">
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-0">User Records</h4>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-circle me-1"></i> Add New User
                </button>
            </div>


            <div class="mb-3">
                <input type="text" id="searchInput" class="form-control search-box" placeholder="Search users...">
            </div>


            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead>
                        <tr>
                            <th class="sortable" data-sort="id">
                                ID 
                            </th>
                            <th class="sortable" data-sort="name">
                                Name 
                            </th>
                            <th class="sortable" data-sort="email">
                                Email 
                            </th>
                            <th class="sortable" data-sort="phone">
                                Phone 
                            </th>
                            <th class="sortable" data-sort="status">
                                Status 
                            </th>

                            <th class="sortable" data-sort="status">
                                CF 
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">

                    </tbody>
                </table>
            </div>


            <nav aria-label="User table pagination">
                <ul class="pagination justify-content-center" id="pagination">

                </ul>
            </nav>
        </div>
    </div>


    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">
                        <i class="bi bi-person-plus-fill me-2"></i>Add New User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="userForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input class="form-control" id="name" type="text" name="name" required
                                aria-describedby="nameHelp">
                            <div class="invalid-feedback" id="error-name"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input class="form-control" type="email" name="email" required>
                            <div class="invalid-feedback" id="error-email"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input class="form-control" type="tel" name="phone" required>
                            <div class="invalid-feedback" id="error-phone"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Gender</label>
                            <div>
                                <label class="me-3"><input type="radio" name="gender" value="male" required>
                                    Male</label>
                                <label class="me-3"><input type="radio" name="gender" value="female">
                                    Female</label>
                                <label class="me-3"><input type="radio" name="gender" value="other">
                                    Other</label>
                            </div>
                            <div class="invalid-feedback d-block" id="error-gender" style="display:none;"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Profile Image</label>
                            <input class="form-control" type="file" name="profile_image" accept="image/*"
                                required>
                            <div class="invalid-feedback" id="error-profile_image"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Additional File</label>
                            <input class="form-control" type="file" name="additional_file">
                            <div class="invalid-feedback" id="error-additional_file"></div>
                        </div>


                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Custom fields</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addCustomFieldBtn">
                                    + Add field
                                </button>
                            </div>

                            <div id="customFieldsContainer" class="vstack gap-2">

                            </div>


                            <template id="customFieldTemplate">
                                <div class="card p-2 custom-field-row">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label">Field label</label>
                                            <input type="text" name="label" class="form-control cf-label"
                                                placeholder="e.g., Birthday, Company Name" />
                                            <div class="invalid-feedback cf-error-label"></div>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Type</label>
                                            <select class="form-select cf-type" name="type">
                                                <option value="text" selected>Text</option>
                                                <option value="date">Date</option>
                                                <option value="number">Number</option>
                                                <option value="email">Email</option>
                                                <option value="url">URL</option>
                                                <option value="select">Select</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4 cf-value-col">
                                            <label class="form-label">Value</label>
                                            <input type="text" name="value" class="form-control cf-value"
                                                placeholder="Enter value" />
                                            <div class="invalid-feedback cf-error-value"></div>
                                        </div>

                                        <div class="col-md-4 d-none cf-options-col">
                                            <label class="form-label">Options (comma separated)</label>
                                            <input type="text" class="form-control cf-options"
                                                placeholder="e.g., Home, Work, Other" />
                                            <div class="form-text">Shown when Type is Select.</div>
                                        </div>

                                        <div class="col-md-1 d-flex">
                                            <button type="button"
                                                class="btn btn-outline-danger w-100 remove-custom-field">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Edit User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editUserForm">
                    <div class="modal-body">
                        <input type="hidden" id="editUserId" name="id">
                        <div class="mb-3">
                            <label for="editFullName" class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" id="editFullName"
                                placeholder="Enter full name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" id="editEmail"
                                placeholder="Enter email address" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPhone" class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" id="editPhone"
                                placeholder="+1 234 567 8900" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Gender</label>
                            <div>
                                <label class="me-3"><input type="radio" name="gender" value="male"
                                        required="">
                                    Male</label>
                                <label class="me-3"><input type="radio" name="gender" value="female">
                                    Female</label>
                                <label class="me-3"><input type="radio" name="gender" value="other">
                                    Other</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Profile Image</label>
                            <img width="100px" src="" id="profile_image_src" alt="">
                            <input class="form-control" type="file" name="profile_image" accept="image/*"
                                >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Additional File</label>
                            <input class="form-control" type="file" name="additional_file">
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" name="status" id="editStatus" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>


                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Custom fields</label>
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    id="addCustomFieldBtnEdit">
                                    + Add field
                                </button>
                            </div>

                            <div id="customFieldsContainerEdit" class="vstack gap-2">

                            </div>


                            <template id="customFieldTemplateEdit">
                                <div class="card p-2 custom-field-row-edit">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label">Field label</label>
                                            <input type="text" name="label" class="form-control cf-label-edit"
                                                placeholder="e.g., Birthday, Company Name" />
                                            <div class="invalid-feedback cf-error-label"></div>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Type</label>
                                            <select class="form-select cf-type-edit" name="type">
                                                <option value="text" selected>Text</option>
                                                <option value="date">Date</option>
                                                <option value="number">Number</option>
                                                <option value="email">Email</option>
                                                <option value="url">URL</option>
                                                <option value="select">Select</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4 cf-value-col-edit">
                                            <label class="form-label">Value</label>
                                            <input type="text" name="value" class="form-control cf-value-edit"
                                                placeholder="Enter value" />
                                            <div class="invalid-feedback cf-error-value"></div>
                                        </div>

                                        <div class="col-md-4 d-none cf-options-col-edit">
                                            <label class="form-label">Options (comma separated)</label>
                                            <input type="text" class="form-control cf-options-edit"
                                                placeholder="e.g., Home, Work, Other" />
                                            <div class="form-text">Shown when Type is Select.</div>
                                        </div>

                                        <div class="col-md-1 d-flex">
                                            <button type="button"
                                                class="btn btn-outline-danger w-100 remove-custom-field-edit">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="updateUser()">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




    <div class="modal fade" id="mergeUserModal" tabindex="-1" aria-labelledby="mergeUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mergeUserModalLabel">
                        <i class="bi bi-person-merge me-2"></i>Merge Contact
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <input type="hidden" id="sourceContactId">


                    <p>You are about to merge the following contact:</p>
                    <div class="card bg-light p-3 mb-4">
                        <h5 id="sourceContactName">[Source Contact Name]</h5>
                        <p class="mb-0 text-muted" id="sourceContactEmail">[source@example.com]</p>
                    </div>

                    <hr>


                    <div class="mb-3">
                        <label for="mergeSearchInput" class="form-label">
                            <strong>Step 1: Find the contact to merge into</strong>
                        </label>
                        <input type="text" class="form-control" id="mergeSearchInput"
                            placeholder="Start typing a name or email...">

                        <div id="mergeSearchResults" class="list-group mt-2"></div>
                    </div>


                    <div id="destinationContactCard" class="mb-3" style="display: none;">
                        <label class="form-label">
                            <strong>Step 2: Confirm destination contact</strong>
                        </label>
                        <div class="card border-primary p-3">
                            <h5 id="destinationContactName"></h5>
                            <p class="mb-0 text-muted" id="destinationContactEmail"></p>

                            <input type="hidden" id="destinationContactId">
                        </div>
                    </div>


                    <div id="mergeWarning" class="alert alert-danger" style="display: none;">
                        <p class="mb-0 fw-bold">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>This action cannot be undone.
                        </p>
                        <p class="mb-0 mt-2">
                            All data from UserId: <strong id="warningSourceContact"></strong> will be moved to <strong
                                id="warningDestinationContact"></strong>.
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmMergeBtn" onclick="confirmMerge()"
                        disabled>
                        Confirm Merge
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteUserModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the user:</p>
                    <h5 class="text-danger" id="deleteUserName"></h5>
                    <p class="text-muted mb-0">This action cannot be undone.</p>
                    <input type="hidden" id="deleteUserId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">Confirm Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const addBtn = document.getElementById('addCustomFieldBtn');
            const container = document.getElementById('customFieldsContainer');
            const tpl = document.getElementById('customFieldTemplate');

            function addCustomField(prefill = {}) {
                const node = tpl.content.firstElementChild.cloneNode(true);

                const labelEl = node.querySelector('.cf-label');
                const typeEl = node.querySelector('.cf-type');
                const valueCol = node.querySelector('.cf-value-col');
                const valueEl = node.querySelector('.cf-value');
                const optionsCol = node.querySelector('.cf-options-col');
                const optionsEl = node.querySelector('.cf-options');
                const removeBtn = node.querySelector('.remove-custom-field');


                if (prefill.label) labelEl.value = prefill.label;
                if (prefill.type) typeEl.value = prefill.type;
                if (prefill.options && Array.isArray(prefill.options)) {
                    optionsEl.value = prefill.options.join(', ');
                }
                if (prefill.value) valueEl.value = prefill.value;

                function applyType() {
                    const t = typeEl.value;

                    const currentValue = (valueEl && valueEl.value) || '';
                    valueCol.innerHTML = `
                        <label class="form-label">Value</label>
                        <input type="text" class="form-control cf-value" placeholder="Enter value" />
                        <div class="invalid-feedback cf-error-value"></div>
                        `;
                    const newValueEl = valueCol.querySelector('.cf-value');

                    if (t === 'date') newValueEl.type = 'date';
                    else if (t === 'number') newValueEl.type = 'number';
                    else if (t === 'email') newValueEl.type = 'email';
                    else if (t === 'url') newValueEl.type = 'url';
                    else if (t === 'select') {

                        const opts = optionsEl.value.split(',').map(s => s.trim()).filter(Boolean);
                        valueCol.innerHTML = `
                        <label class="form-label">Value</label>
                        <select class="form-select cf-value">
                        ${opts.map(o => `<option value="${o}">${o}</option>`).join('')}
                        </select>
                        <div class="invalid-feedback cf-error-value"></div>
                    `;
                    }


                    const restored = valueCol.querySelector('.cf-value');
                    if (restored && currentValue && restored.tagName === 'INPUT') {
                        restored.value = currentValue;
                    }
                }


                function toggleOptions() {
                    const isSelect = typeEl.value === 'select';
                    optionsCol.classList.toggle('d-none', !isSelect);
                    applyType();
                }

                typeEl.addEventListener('change', toggleOptions);
                optionsEl.addEventListener('input', () => {
                    if (typeEl.value === 'select') applyType();
                });

                removeBtn.addEventListener('click', () => node.remove());


                container.appendChild(node);
                toggleOptions();
            }


            const addBtnEdit = document.getElementById('addCustomFieldBtnEdit');
            const containerEdit = document.getElementById('customFieldsContainerEdit');
            const tplEdit = document.getElementById('customFieldTemplateEdit');

            function addCustomFieldEdit(prefill = {}) {
                const node = tplEdit.content.firstElementChild.cloneNode(true);

                const labelEl = node.querySelector('.cf-label-edit');
                const typeEl = node.querySelector('.cf-type-edit');
                const valueCol = node.querySelector('.cf-value-col-edit');
                const valueEl = node.querySelector('.cf-value-edit');
                const optionsCol = node.querySelector('.cf-options-col-edit');
                const optionsEl = node.querySelector('.cf-options-edit');
                const removeBtn = node.querySelector('.remove-custom-field-edit');


                if (prefill.label) labelEl.value = prefill.label;
                if (prefill.type) typeEl.value = prefill.type;
                if (prefill.options && Array.isArray(prefill.options)) {
                    optionsEl.value = prefill.options.join(', ');
                }
                if (prefill.value) valueEl.value = prefill.value;

                function applyType() {
                    const t = typeEl.value;

                    const currentValue = (valueEl && valueEl.value) || '';
                    valueCol.innerHTML = `
                        <label class="form-label">Value</label>
                        <input type="text" class="form-control cf-value" placeholder="Enter value" />
                        <div class="invalid-feedback cf-error-value"></div>
                        `;
                    const newValueEl = valueCol.querySelector('.cf-value');

                    if (t === 'date') newValueEl.type = 'date';
                    else if (t === 'number') newValueEl.type = 'number';
                    else if (t === 'email') newValueEl.type = 'email';
                    else if (t === 'url') newValueEl.type = 'url';
                    else if (t === 'select') {

                        const opts = optionsEl.value.split(',').map(s => s.trim()).filter(Boolean);
                        valueCol.innerHTML = `
                        <label class="form-label">Value</label>
                        <select class="form-select cf-value">
                        ${opts.map(o => `<option value="${o}">${o}</option>`).join('')}
                        </select>
                        <div class="invalid-feedback cf-error-value"></div>
                    `;
                    }


                    const restored = valueCol.querySelector('.cf-value');
                    if (restored && currentValue && restored.tagName === 'INPUT') {
                        restored.value = currentValue;
                    }
                }


                function toggleOptions() {
                    const isSelect = typeEl.value === 'select';
                    optionsCol.classList.toggle('d-none', !isSelect);
                    applyType();
                }

                typeEl.addEventListener('change', toggleOptions);
                optionsEl.addEventListener('input', () => {
                    if (typeEl.value === 'select') applyType();
                });

                removeBtn.addEventListener('click', () => node.remove());


                containerEdit.appendChild(node);
                toggleOptions();
            }

            addBtn.addEventListener('click', () => addCustomField());
            addBtnEdit.addEventListener('click', () => addCustomFieldEdit());


            addCustomField({
                label: 'Birthday',
                type: 'date'
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('userForm');
            const result = document.getElementById('result');

            function clearErrors() {

                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                form.querySelectorAll('.invalid-feedback').forEach(el => {
                    el.textContent = '';
                    el.style.display = '';
                });

                const genderMsg = document.getElementById('error-gender');
                if (genderMsg) genderMsg.style.display = 'none';
            }

            function setFieldError(field, message) {

                const input = form.querySelector(`[name="${field}"]`);
                const errorBox = document.getElementById(`error-${field}`);

                if (field === 'gender') {

                    form.querySelectorAll('input[name="gender"]').forEach(r => r.classList.add('is-invalid'));
                    if (errorBox) {
                        errorBox.textContent = message;
                        errorBox.style.display = 'block';
                    }
                    return;
                }

                if (input) {
                    input.classList.add('is-invalid');
                }
                if (errorBox) {
                    errorBox.textContent = message;
                }
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                clearErrors();

                const formData = new FormData(form);

                const customFields = Array.from(document.querySelectorAll(
                    '#customFieldsContainer .custom-field-row')).map(row => {
                    const label = row.querySelector('.cf-label')?.value?.trim() || '';
                    const type = row.querySelector('.cf-type')?.value || 'text';
                    const valueEl = row.querySelector('.cf-value');
                    const value = valueEl ? (valueEl.value ?? '').toString().trim() : '';
                    const opts = row.querySelector('.cf-options')?.value || '';
                    const options = opts.split(',').map(s => s.trim()).filter(Boolean);
                    return {
                        label,
                        type,
                        value,
                        options
                    };
                }).filter(f => f.label !== '');
                formData.append('custom_fields', JSON.stringify(customFields));

                const csrf = document.querySelector('meta[name="csrf-token"]');
                if (csrf) formData.append('_token', csrf.getAttribute(
                    'content'));

                try {
                    const res = await fetch('/contact', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await res.json();
                    console.log(data);
                    if (res.ok) {


                        const modal = bootstrap.Modal.getInstance(document.getElementById(
                            'addUserModal'));
                        modal.hide();
                        const newUser = data.data;
                        users.push(newUser);
                        filteredUsers = [...users];
                        loadUsers();
                        showToast('Success', `User "${data.data.name}" has been added successfully!`,
                            'success');
                        form.reset();
                    } else if (res.status === 422 && data && data.errors) {

                        Object.entries(data.errors).forEach(([field, messages]) => {
                            setFieldError(field, messages[0]);
                        });

                        showToast('Success', `Please correct the highlighted errors`, 'warning');
                    } else {

                        showToast('Success', `Submission failed.`, 'warning');
                    }
                } catch (err) {
                    console.log(err, result);

                    showToast('Success', `Network error. Please try again`, 'danger');

                }
            });
        });

        let allUsers = [];
        let users = [];
        let filteredUsers = [];
        let currentPage = 1;
        const recordsPerPage = 10;

        let serverMode = true;
        let paginationMeta = {
            current_page: 1,
            last_page: 1,
            per_page: recordsPerPage,
            total: 0
        };

        async function loadUsers(page = 1) {
            const url = serverMode ?
                `/contact-list?page=${page}&per_page=${recordsPerPage}` :
                `/contact-list`;

            const res = await fetch(url, {
                headers: {
                    Accept: 'application/json'
                }
            });
            const json = await res.json();

            if (serverMode) {
                allUsers = json.data || [];
                users = json.data || [];
                filteredUsers = allUsers;
                paginationMeta = {
                    current_page: json.current_page,
                    last_page: json.last_page,
                    per_page: json.per_page,
                    total: json.total
                };
                currentPage = paginationMeta.current_page;
            } else {
                allUsers = json.data || [];
                users = json.data || [];
                filteredUsers = allUsers;
                currentPage = 1;
                const last = Math.max(1, Math.ceil(filteredUsers.length / recordsPerPage));
                paginationMeta = {
                    current_page: 1,
                    last_page: last,
                    per_page: recordsPerPage,
                    total: filteredUsers.length
                };
            }

            renderTable();
            renderPagination();
        }

        function goToPage(page) {
            if (serverMode) {
                if (page < 1 || page > paginationMeta.last_page) return;
                loadUsers(page);
            } else {
                const last = Math.max(1, Math.ceil(filteredUsers.length / recordsPerPage));
                currentPage = Math.min(Math.max(1, page), last);
                renderTable();
                renderPagination();
            }
        }

        document.addEventListener('DOMContentLoaded', () => loadUsers(1));


        function renderTable() {
            const tbody = document.getElementById('userTableBody');
            tbody.innerHTML = '';

            const startIndex = (currentPage - 1) * recordsPerPage;
            const endIndex = startIndex + recordsPerPage;
            const rows = serverMode ? filteredUsers : filteredUsers.slice(startIndex, endIndex);
            console.log(rows);
            if (rows.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No users found</td></tr>';
            } else {

                let html = '';
                users.forEach(user => {

                    const userName = user.name || `${user.first_name || ''} ${user.last_name || ''}`.trim();

                    html += `
        <tr>
            <td>#${user.id}</td>
            <td>${userName}</td>
            <td>
                ${user.merged_emails?.length > 1 ? 
                
                `${user.merged_emails.map(email=>email.email).join('<br>')}`
                :
                `${user.email}`
                }
                
                </td>
            <td>

                  ${user.merged_phones?.length > 1 ? 
                
                `${user.merged_phones.map(phone=>phone.phone).join('<br>')}`
                :
                `${user.phone}`
                }
                
                </td>
            <td>
                <span class="status-badge ${user.status === 'Active' ? 'bg-success' : 'bg-warning'} text-white">
                    ${user.status || ''}
                </span>
            </td>
            <td>
                ${user.custom_field?.length > 0 ? 
                
               `${user.custom_field.map(data => `<strong>${data.label}:</strong> ${data.value}`).join('<br>')}`
                :
                ``
                }
                </td>
            <td>
                ${user.status === 'merged' ?
                   
                    `<div class="text-muted small">
                                            <p class="mb-0"><strong>Merged To UserId:</strong> #${user.merged_into_id || 'N/A'}</p>
                                            <p class="mb-0"><strong>Merged At:</strong> ${user.merged_at ? new Date(user.merged_at).toLocaleString() : 'N/A'}</p>
                                        </div>
                                                                                    ` :
                  
                    `<button class="btn btn-sm btn-primary btn-action" onclick="openEditModal(${user.id})" title="Edit">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-action" onclick="openDeleteModal(${user.id}, '${userName}')" title="Delete">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                        <button class="btn btn-sm btn-warning btn-action" onclick="openMergeModal(${user.id}, '${userName}', '${user.email}')" title="Merge">
                                            <i class="bi bi-person-merge"></i> Merge
                                        </button>`
                }
            </td>
        </tr>
    `;
                });
                tbody.innerHTML = html;
            }

            renderPagination();
        }


        function renderPagination() {
            const pagination = document.getElementById('pagination');
            const totalPages = Math.ceil(filteredUsers.length / recordsPerPage);

            if (totalPages <= 1) {
                pagination.innerHTML = '';
                return;
            }

            let paginationHTML = '';


            paginationHTML += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;">Previous</a>
            </li>
             `;


            for (let i = 1; i <= totalPages; i++) {
                paginationHTML += `
                <li class="page-item ${currentPage === i ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
                </li>
            `;
            }


            paginationHTML += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;">Next</a>
            </li>
         `;

            pagination.innerHTML = paginationHTML;
        }


        function changePage(page) {
            const totalPages = Math.ceil(filteredUsers.length / recordsPerPage);
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderTable();
        }


        const searchInput = document.getElementById('searchInput');


        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value;

            const searchUrl = `/contact-list?search=${searchTerm}&page=1&per_page=${recordsPerPage}`;


            loadUsers(1, searchTerm);
        });


        async function loadUsers(page = 1, searchTerm = '') {
            let url = serverMode ?
                `/contact-list?page=${page}&per_page=${recordsPerPage}` :
                `/contact-list`;

            // Add search term to the URL if it exists
            if (serverMode && searchTerm) {
                url += `&search=${encodeURIComponent(searchTerm)}`;
            }

            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });


            const json = await res.json();


            if (serverMode) {
                allUsers = json.data || [];
                users = json.data || [];
                filteredUsers = allUsers;
                paginationMeta = {
                    current_page: json.current_page,
                    last_page: json.last_page,
                    per_page: json.per_page,
                    total: json.total
                };
                currentPage = paginationMeta.current_page;
            } else {
                allUsers = json.data || [];

                const lowercasedSearchTerm = searchTerm.toLowerCase();
                const filtered = !searchTerm ? allUsers : allUsers.filter(user => {
                    return user.first_name.toLowerCase().includes(lowercasedSearchTerm) ||
                        user.last_name.toLowerCase().includes(lowercasedSearchTerm) ||
                        user.email.toLowerCase().includes(lowercasedSearchTerm);
                });

                users = filtered;
                filteredUsers = users;
                currentPage = 1;
                const last = Math.max(1, Math.ceil(filteredUsers.length / recordsPerPage));
                paginationMeta = {
                    current_page: 1,
                    last_page: last,
                    per_page: recordsPerPage,
                    total: filteredUsers.length
                };
            }


            renderTable();
            renderPagination();
        }


        function showToast(title, message, type = 'success') {
            const toastEl = document.getElementById('toastNotification');
            const toast = new bootstrap.Toast(toastEl);

            const toastHeader = toastEl.querySelector('.toast-header');
            const toastIcon = document.getElementById('toastIcon');


            toastHeader.className = 'toast-header';
            toastIcon.className = 'bi me-2';

            if (type === 'success') {
                toastHeader.classList.add('bg-success', 'text-white');
                toastIcon.classList.add('bi-check-circle-fill');
            } else if (type === 'danger') {
                toastHeader.classList.add('bg-danger', 'text-white');
                toastIcon.classList.add('bi-x-circle-fill');
            } else if (type === 'warning') {
                toastHeader.classList.add('bg-warning', 'text-dark');
                toastIcon.classList.add('bi-exclamation-triangle-fill');
            }

            document.getElementById('toastTitle').textContent = title;
            document.getElementById('toastMessage').textContent = message;
            toast.show();
        }

        function openEditModal(id) {
            const user = users.find(u => u.id === id);

            console.log("user", user);
            if (!user) return;

            document.getElementById('editUserId').value = user.id;
            document.getElementById('editFullName').value = user.name;
            document.getElementById('editEmail').value = user.email;
            document.getElementById('editPhone').value = user.phone;
            document.getElementById('editStatus').value = user.status;
            document.getElementById('profile_image_src').src = `/storage/${user.profile_image_path}`;


            const editModalEl = document.getElementById('editUserModal');
            const genderRadioButton = editModalEl.querySelector(`input[name="gender"][value="${user.gender}"]`);


            if (genderRadioButton) {
                genderRadioButton.checked = true;
            } else {
                editModalEl.querySelectorAll('input[name="gender"]').forEach(radio => radio.checked = false);
            }

            const cf = user.custom_field;
            var prefillData = {};
            console.log(cf);

            if (cf) {

                for (let i = 0; i < cf.length; i++) {
                    if (cf[i].type == 'select') {
                        prefillData[i] = {
                            'label': cf[i].label,
                            'type': cf[i].type,
                            'value': cf[i].value,
                            'options': cf[i].options,
                        }
                    } else {
                        prefillData[i] = {
                            'label': cf[i].label,
                            'type': cf[i].type,
                            'value': cf[i].value,
                        }
                    }
                }


                document.getElementById('customFieldsContainerEdit').innerHTML = "";
                const container = document.getElementById('customFieldsContainerEdit');
                const tpl = document.getElementById('customFieldTemplateEdit');

                function addCustomFieldEdit(prefill = {}) {
                    const node = tpl.content.firstElementChild.cloneNode(true);

                    const labelEl = node.querySelector('.cf-label-edit');
                    const typeEl = node.querySelector('.cf-type-edit');
                    const valueCol = node.querySelector('.cf-value-col-edit');
                    const valueEl = node.querySelector('.cf-value-edit');
                    const optionsCol = node.querySelector('.cf-options-col-edit');
                    const optionsEl = node.querySelector('.cf-options-edit');
                    const removeBtn = node.querySelector('.remove-custom-field-edit');

                    console.log(prefill);

                    if (prefill.label) labelEl.value = prefill.label;
                    if (prefill.type) typeEl.value = prefill.type;
                    if (prefill.options) {
                        optionsEl.value = JSON.parse(prefill.options).join(', ');
                    }
                    if (prefill.value) valueEl.value = prefill.value;

                    function applyType() {
                        const t = typeEl.value;

                        const currentValue = (valueEl && valueEl.value) || '';
                        valueCol.innerHTML = `
                            <label class="form-label">Value</label>
                            <input type="text" class="form-control cf-value-edit" placeholder="Enter value" />
                            <div class="invalid-feedback cf-error-value"></div>
                            `;
                        const newValueEl = valueCol.querySelector('.cf-value-edit');

                        if (t === 'date') newValueEl.type = 'date';
                        else if (t === 'number') newValueEl.type = 'number';
                        else if (t === 'email') newValueEl.type = 'email';
                        else if (t === 'url') newValueEl.type = 'url';
                        else if (t === 'select') {

                            const opts = optionsEl.value.split(',').map(s => s.trim()).filter(Boolean);
                            valueCol.innerHTML = `
                                <label class="form-label">Value</label>
                                <select class="form-select cf-value-edit">
                                ${opts.map(o => `<option value="${o}">${o}</option>`).join('')}
                                </select>
                                <div class="invalid-feedback cf-error-value"></div>
                            `;
                        }


                        const restored = valueCol.querySelector('.cf-value-edit');
                        if (restored && currentValue && restored.tagName === 'INPUT') {
                            restored.value = currentValue;
                        }
                    }


                    function toggleOptions() {
                        const isSelect = typeEl.value === 'select';
                        optionsCol.classList.toggle('d-none', !isSelect);
                        applyType();
                    }

                    typeEl.addEventListener('change', toggleOptions);
                    optionsEl.addEventListener('input', () => {
                        if (typeEl.value === 'select') applyType();
                    });

                    removeBtn.addEventListener('click', () => node.remove());


                    container.appendChild(node);
                    toggleOptions();
                }

                for (let i = 0; i < cf.length; i++) {

                    console.log(prefillData[i]);
                    addCustomFieldEdit(prefillData[i]);
                }


                console.log(prefillData);
            }

            const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
            modal.show();
        }

        async function updateUser() {
            const form = document.getElementById('editUserForm');
            const contactId = document.getElementById('editUserId').value;
            const submitButton = form.querySelector('.btn-primary');


            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            submitButton.disabled = true;
            submitButton.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

            const formData = new FormData(form);

            const customFields = Array.from(document.querySelectorAll(
                '#customFieldsContainerEdit .custom-field-row-edit')).map(row => {
                const label = row.querySelector('.cf-label-edit')?.value?.trim() || '';
                const type = row.querySelector('.cf-type-edit')?.value || 'text';
                const valueEl = row.querySelector('.cf-value-edit');
                const value = valueEl ? (valueEl.value ?? '').toString().trim() : '';
                const opts = row.querySelector('.cf-options-edit')?.value || '';
                const options = opts.split(',').map(s => s.trim()).filter(Boolean);
                return {
                    label,
                    type,
                    value,
                    options
                };
            }).filter(f => f.label !== '');
            formData.append('custom_fields', JSON.stringify(customFields));

            try {

                const response = await fetch(`/contacts/${contactId}/update`, {
                    method: 'POST',
                    headers: {

                        'Accept': 'application/json',

                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: formData
                });

                const result = await response.json();

                if (!response.ok) {

                    throw new Error(result.message || 'An error occurred during the update.');
                }


                const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                modal.hide();


                loadUsers();


                showToast('Success', result.message, 'success');

            } catch (error) {

                console.error('Update failed:', error);
                showToast('Error', error.message, 'danger');

            } finally {

                submitButton.disabled = false;
                submitButton.innerHTML = 'Save Changes';
            }
        }



        function openMergeModal(id) {
            const user = users.find(u => u.id === id);
            if (!user) return;

            document.getElementById('deleteUserId').value = user.id;
            document.getElementById('deleteUserName').textContent = user.name;

            sourceContactIdInput.value = user.id;
            sourceContactNameEl.textContent = user.name;
            sourceContactEmailEl.textContent = user.email;
            warningSourceContact.textContent = user.id;

            const modal = new bootstrap.Modal(document.getElementById('mergeUserModal'));
            modal.show();
        }


        function openDeleteModal(id) {
            const user = users.find(u => u.id === id);
            if (!user) return;

            document.getElementById('deleteUserId').value = user.id;
            document.getElementById('deleteUserName').textContent = user.name;

            const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
            modal.show();
        }



        const sourceContactIdInput = document.getElementById('sourceContactId');
        const sourceContactNameEl = document.getElementById('sourceContactName');
        const sourceContactEmailEl = document.getElementById('sourceContactEmail');
        const mergeSearchInput = document.getElementById('mergeSearchInput');
        const mergeSearchResultsEl = document.getElementById('mergeSearchResults');
        const destinationContactCard = document.getElementById('destinationContactCard');
        const destinationContactIdInput = document.getElementById('destinationContactId');
        const destinationContactNameEl = document.getElementById('destinationContactName');
        const destinationContactEmailEl = document.getElementById('destinationContactEmail');
        const mergeWarning = document.getElementById('mergeWarning');
        const warningSourceContact = document.getElementById('warningSourceContact');
        const warningDestinationContact = document.getElementById('warningDestinationContact');
        const confirmMergeBtn = document.getElementById('confirmMergeBtn');

        async function searchContactsForMerge() {
            const searchTerm = mergeSearchInput.value.trim();
            const sourceId = sourceContactIdInput.value;


            if (searchTerm.length < 2) {
                mergeSearchResultsEl.innerHTML = '';
                return;
            }

            try {

                const response = await fetch(
                    `/contacts/search-for-merge?query=${encodeURIComponent(searchTerm)}&exclude_id=${sourceId}`);
                if (!response.ok) {
                    throw new Error('Network error');
                }
                const contacts = await response.json();


                renderMergeSearchResults(contacts);

            } catch (error) {
                mergeSearchResultsEl.innerHTML = '<div class="list-group-item">Error loading results.</div>';
            }
        }

        function renderMergeSearchResults(contacts) {
            mergeSearchResultsEl.innerHTML = '';

            if (contacts.length === 0) {
                mergeSearchResultsEl.innerHTML = '<div class="list-group-item text-muted">No contacts found.</div>';
                return;
            }

            contacts.forEach(contact => {
                const contactEl = document.createElement('a');
                contactEl.href = '#';
                contactEl.className = 'list-group-item list-group-item-action';
                contactEl.innerHTML =
                    `<strong> ${contact.name}</strong><br><small class="text-muted">${contact.email}</small>`;

                contactEl.onclick = (e) => {
                    e.preventDefault();
                    selectDestinationContact(contact);
                };
                mergeSearchResultsEl.appendChild(contactEl);
            });
        }

        function selectDestinationContact(contact) {
            console.log(contact);
            destinationContactIdInput.value = `${contact.id}`;
            destinationContactNameEl.textContent = `${contact.name}`;
            destinationContactEmailEl.textContent = contact.email;
            warningDestinationContact.textContent = `${contact.id}`;

            destinationContactCard.style.display = 'block';
            mergeWarning.style.display = 'block';
            confirmMergeBtn.disabled = false;
            mergeSearchResultsEl.innerHTML = '';
            mergeSearchInput.value = '';
        }

        async function confirmMerge() {
            const sourceId = sourceContactIdInput.value;
            const destinationId = destinationContactIdInput.value;

            if (!sourceId || !destinationId) {
                alert('Error: Source or destination contact is missing.');
                return;
            }

            confirmMergeBtn.disabled = true;
            confirmMergeBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Merging...';

            try {

                const response = await fetch('/contacts/merge', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        source_contact_id: sourceId,
                        destination_contact_id: destinationId
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to merge contacts.');
                }


                alert('Contacts merged successfully!');

                loadUsers();

            } catch (error) {
                console.error('Merge failed:', error);
                alert(`Error: ${error.message}`);
            } finally {
                confirmMergeBtn.disabled = false;
                confirmMergeBtn.innerHTML = 'Confirm Merge';
            }
        }


        function resetMergeModal() {
            sourceContactIdInput.value = '';
            destinationContactIdInput.value = '';
            mergeSearchInput.value = '';
            mergeSearchResultsEl.innerHTML = '';
            destinationContactCard.style.display = 'none';
            mergeWarning.style.display = 'none';
            confirmMergeBtn.disabled = true;
        }



        mergeSearchInput.addEventListener('input', searchContactsForMerge);

        const deleteUserIdInput = document.getElementById('deleteUserId');
        const deleteUserNameEl = document.getElementById('deleteUserName');


        async function confirmDelete() {

            const contactId = parseInt(document.getElementById('deleteUserId').value);
            const user = users.find(u => u.id === contactId);

            const deleteButton = document.querySelector('.btn-danger[onclick="confirmDelete()"]');

            if (!contactId) {
                alert('Error: Contact ID not found.');
                return;
            }


            deleteButton.disabled = true;
            deleteButton.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';

            try {

                const response = await fetch(`/contacts/${contactId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',

                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to delete the contact.');
                }


                console.log('Contact deleted successfully');

                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteUserModal'));
                modal.hide();


                loadUsers();

            } catch (error) {
                console.error('Delete failed:', error);
                alert(`Error: ${error.message}`);
            } finally {

                deleteButton.disabled = false;
                deleteButton.innerHTML = 'Confirm Delete';
            }
        }
    </script>
</body>

</html>
