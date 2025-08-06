@extends('layouts.default-layout')
@section('content')
<!-- Main Content -->
<!-- Manage Roles Section -->
<div class="mt-10 w-full max-w-screen-xl mx-auto px-4 min-h-screen">
    <h2 class="text-2xl font-bold text-blue-700 mb-4">Manage Roles</h2>

    <div class="overflow-x-auto shadow rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700">
        <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-700 text-left text-gray-800 dark:text-gray-200">
                    <th class="px-6 py-3 border-b border-gray-300 dark:border-gray-700">Account Name</th>
                    <th class="px-6 py-3 border-b border-gray-300 dark:border-gray-700">Email Address</th>
                    <th class="px-6 py-3 border-b border-gray-300 dark:border-gray-700">Role</th>
                </tr>
            </thead>
            <tbody>
                <tr class="cursor-pointer hover:bg-blue-100 dark:hover:bg-gray-600 transition" onclick="openRoleModal('John Doe', 'john.doe@example.com', 'User')">
                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 font-medium">John Doe</td>
                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-300">john.doe@example.com</td>
                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-blue-700 font-semibold">User</td>
                </tr>
                <tr class="cursor-pointer hover:bg-blue-100 dark:hover:bg-gray-600 transition" onclick="openRoleModal('Jane Smith', 'jane.smith@example.com', 'Frontdesk')">
                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 font-medium">Jane Smith</td>
                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-300">jane.smith@example.com</td>
                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-blue-700 font-semibold">Frontdesk</td>
                </tr>
                <tr class="cursor-pointer hover:bg-blue-100 dark:hover:bg-gray-600 transition" onclick="openRoleModal('Alice Johnson', 'alice.johnson@example.com', 'Admin')">
                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 font-medium">Alice Johnson</td>
                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-300">alice.johnson@example.com</td>
                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-blue-700 font-semibold">Admin</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Manage Personal Information Section -->
    <div class="mt-10 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h2 class="text-xl font-bold cursor-pointer mb-4" onclick="toggleDropdown('personalInfoDropdown')">Manage Personal Information</h2>
        <div id="personalInfoDropdown" class="space-y-2 hidden">
            <button onclick="showChangeNumberModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded w-full text-left">Change Number</button>
            <button onclick="showChangePasswordModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded w-full text-left">Change Password</button>
        </div>
    </div>

    <!-- Backup and Recovery Section -->
    <div class="mt-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h2 class="text-xl font-bold cursor-pointer mb-4" onclick="toggleDropdown('backupRecoveryDropdown')">Backup and Recovery</h2>
        <div id="backupRecoveryDropdown" class="space-y-2 hidden">
            <button onclick="showCreateBackupModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded w-full text-left">Create Backup</button>
            <button onclick="showRestoreBackupModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded w-full text-left">Restore Backup</button>
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded w-full text-left">Recover Account</button>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Role Assignment Modal -->
<div id="roleModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-md w-96">
        <h3 class="text-lg font-bold mb-4">Assign Role</h3>
        <p class="mb-4">Assign a new role for <span id="roleUser" class="font-semibold text-blue-700"></span>:</p>
        <div class="space-y-2">
            <button onclick="assignRole('Frontdesk')" class="w-full px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded">Frontdesk</button>
            <button onclick="assignRole('Admin')" class="w-full px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded">Admin</button>
        </div>
        <div class="flex justify-end mt-4">
            <button onclick="closeRoleModal()" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
        </div>
    </div>
</div>
<!-- personal info Modal -->
<div id="changeNumberModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-md w-96">
        <h3 class="text-lg font-bold mb-4">Change Contact Number</h3>
        <input type="text" id="newNumber" class="w-full p-2 border rounded mb-4" placeholder="Enter new number">
        <p class="text-sm text-gray-600 mb-4">Are you sure? This will change the messaging number used.</p>
        <div class="flex justify-end gap-2">
            <button onclick="closeModals()" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
            <button onclick="submitNumberChange()" class="px-4 py-2 bg-green-500 text-white rounded">Confirm</button>
        </div>
    </div>
</div>

<div id="changePasswordModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-md w-96">
        <h3 class="text-lg font-bold mb-4">Change Password</h3>
        <input type="password" id="currentPass" class="w-full p-2 border rounded mb-2" placeholder="Current Password">
        <input type="password" id="newPass" class="w-full p-2 border rounded mb-2" placeholder="New Password">
        <input type="password" id="confirmPass" class="w-full p-2 border rounded mb-4" placeholder="Confirm New Password">
        <p class="text-sm text-gray-600 mb-4">Are you sure you want to change your password?</p>
        <div class="flex justify-end gap-2">
            <button onclick="closeModals()" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
            <button onclick="submitPasswordChange()" class="px-4 py-2 bg-green-500 text-white rounded">Confirm</button>
        </div>
    </div>
</div>

<!-- Backup and Restore Modals -->
<div id="createBackupModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-md w-80">
        <h3 class="text-lg font-bold mb-4">Create Backup</h3>
        <button class="bg-blue-500 hover:bg-blue-600 text-white w-full p-2 mb-2 rounded">Local Save</button>
        <button class="bg-blue-500 hover:bg-blue-600 text-white w-full p-2 rounded">Cloud Save</button>
        <div class="flex justify-end mt-4">
            <button onclick="closeModals()" class="px-4 py-2 bg-gray-300 rounded">Close</button>
        </div>
    </div>
</div>

<div id="restoreBackupModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-md w-80">
        <h3 class="text-lg font-bold mb-4">Restore Backup</h3>
        <button class="bg-blue-500 hover:bg-blue-600 text-white w-full p-2 mb-2 rounded">Restore from Local</button>
        <button class="bg-blue-500 hover:bg-blue-600 text-white w-full p-2 rounded">Restore from Cloud</button>
        <div class="flex justify-end mt-4">
            <button onclick="closeModals()" class="px-4 py-2 bg-gray-300 rounded">Close</button>
        </div>
    </div>
</div>

<div id="successToast" class="hidden fixed bottom-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow z-50">
    Successfully updated!
</div>

<!-- Scripts -->
<script>
    function openRoleModal(userName, email, currentRole) {
        document.getElementById('roleUser').innerText = userName;
        document.getElementById('roleModal').classList.remove('hidden');

        // Store email if needed for real updates (currently unused)
        roleModalData = {
            name: userName,
            email: email,
            role: currentRole
        };
    }

    function closeRoleModal() {
        document.getElementById('roleModal').classList.add('hidden');
    }

    function assignRole(newRole) {
        alert(`Assigned ${newRole} role to ${roleModalData.name} (${roleModalData.email})`);
        closeRoleModal();

        // Update table or send to server here
        // Example: update table textContent or make a fetch/AJAX call
    }

    // Temporary global to store modal context
    let roleModalData = {};


    function toggleDropdown(id) {
        document.getElementById(id).classList.toggle('hidden');
    }

    function showChangeNumberModal() {
        document.getElementById('changeNumberModal').classList.remove('hidden');
    }

    function showChangePasswordModal() {
        document.getElementById('changePasswordModal').classList.remove('hidden');
    }

    function showCreateBackupModal() {
        document.getElementById('createBackupModal').classList.remove('hidden');
    }

    function showRestoreBackupModal() {
        document.getElementById('restoreBackupModal').classList.remove('hidden');
    }

    function closeModals() {
        document.getElementById('changeNumberModal').classList.add('hidden');
        document.getElementById('changePasswordModal').classList.add('hidden');
        document.getElementById('createBackupModal').classList.add('hidden');
        document.getElementById('restoreBackupModal').classList.add('hidden');
    }

    function submitNumberChange() {
        closeModals();
        showSuccessToast();
    }

    function submitPasswordChange() {
        const newPass = document.getElementById('newPass').value;
        const confirmPass = document.getElementById('confirmPass').value;
        if (newPass !== confirmPass) {
            alert("New password and confirm password do not match.");
            return;
        }
        closeModals();
        showSuccessToast();
    }

    function showSuccessToast() {
        const toast = document.getElementById('successToast');
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 2000);
    }
</script>

@endsection