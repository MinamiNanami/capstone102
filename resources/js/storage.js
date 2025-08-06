// Function to admit a new client and store it in localStorage
function admitClient() {

    const ownerName = document.getElementById("ownerName").value;
    const contactNumber = document.getElementById("contactNumber").value;
    const email = document.getElementById("email").value;
    const date = document.getElementById("date").value;
    const address = document.getElementById("address").value;
    const petType = document.getElementById("petType").value;
    const breed = document.getElementById("breed").value;
    const gender = document.getElementById("gender").value;
    const birthday = document.getElementById("birthday").value;
    const markings = document.getElementById("markings").value;
    const disease = document.getElementById("disease").value;
    const history = document.getElementById("history").value;

    if (!ownerName || !contactNumber || !email || !date || ! address || !petType || !breed || !gender || !birthday) {
        alert("Please fill in all required fields.");
        document.getElementById("loadingSpinner").classList.add("hidden");
        return;
    }

    const clientData = {
        ownerName,
        contactNumber,
        email,
        date,
        address,
        petType,
        breed,
        gender,
        birthday,
        markings,
        disease,
        history
    };

    let clients = JSON.parse(localStorage.getItem("clients")) || [];
    // Add the new client to the beginning of the array (new data will appear first)
    clients.unshift(clientData);

    // Save the updated array back to localStorage
    localStorage.setItem("clients", JSON.stringify(clients));

    // Reset the form fields after admitting
    resetForm();

    // Optional: Reload the client data in the table
    loadClientData();
}

// Function to reset form inputs after admission
function resetForm() {
    document.getElementById("ownerName").value = "";
    document.getElementById("contactNumber").value = "";
    document.getElementById("email").value = "";
    document.getElementById("date").value = "";
    document.getElementById("address").value = "";
    document.getElementById("petType").value = "";
    document.getElementById("breed").value = "";
    document.getElementById("gender").value = "";
    document.getElementById("birthday").value = "";
    document.getElementById("markings").value = "";
    document.getElementById("disease").value = "";
    document.getElementById("history").value = "";
}

function loadClientData() {
    const clients = JSON.parse(localStorage.getItem("clients")) || [];
    const tableBody = document.querySelector("#clientsTable tbody");
    tableBody.innerHTML = '';

    clients.forEach((client, index) => {
        const row = document.createElement("tr");
        row.onclick = () => showClientDetails(client, index);
        row.innerHTML = `
            <td class="border border-gray-300 dark:border-gray-600 p-2">${client.date}</td>
            <td class="border border-gray-300 dark:border-gray-600 p-2">${client.ownerName}</td>
            <td class="border border-gray-300 dark:border-gray-600 p-2">${client.petType}</td>
            <td class="border border-gray-300 dark:border-gray-600 p-2">${client.breed}</td>
            <td class="border border-gray-300 dark:border-gray-600 p-2">${client.gender}</td>
            <td class="border border-gray-300 dark:border-gray-600 p-2">${client.disease}</td>
        `;
        tableBody.appendChild(row);
    });
}

function filterTable() {
    const searchInput = document.getElementById("searchInput").value.toLowerCase();
    const filterOption = document.getElementById("filterOption").value;
    const rows = document.querySelectorAll("#clientsTable tbody tr");

    rows.forEach(row => {
        const cells = row.querySelectorAll("td");
        let match = false;
        switch (filterOption) {
            case "Owner's Name":
                match = cells[0].textContent.toLowerCase().includes(searchInput);
                break;
            case "Pet Type":
                match = cells[1].textContent.toLowerCase().includes(searchInput);
                break;
            case "Breed":
                match = cells[2].textContent.toLowerCase().includes(searchInput);
                break;
            case "Gender":
                match = cells[3].textContent.toLowerCase().includes(searchInput);
                break;
            case "Disease":
                match = cells[4].textContent.toLowerCase().includes(searchInput);
                break;
            default:
                match = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(searchInput));
                break;
        }
        row.style.display = match ? "" : "none";
    });
}

function showClientDetails(client, index) {
    const fields = [
        { id: "modalOwnerName", key: "ownerName" },
        { id: "modalContactNumber", key: "contactNumber" },
        { id: "modalEmail", key: "email" },
        { id: "modalDate", key: "date" },
        { id: "modalAddress", key: "address" },
        { id: "modalPetType", key: "petType" },
        { id: "modalBreed", key: "breed" },
        { id: "modalGender", key: "gender" },
        { id: "modalBirthday", key: "birthday" },
        { id: "modalMarkings", key: "markings" },
        { id: "modalDisease", key: "disease" }
    ];

    // Set all fields as non-editable
    fields.forEach(field => {
        const el = document.getElementById(field.id);
        el.textContent = client[field.key] || "";
        el.removeAttribute("contenteditable");
    });

    const historyEl = document.getElementById("modalHistory");
    historyEl.value = client.history || "";
    historyEl.setAttribute("readonly", true);  // Ensure history is readonly
    

    window.clientIndex = index;
    document.getElementById("clientModal").classList.remove("hidden");
}

let isEditing = false;

function editClient() {
    const editBtn = document.getElementById("editButton");
    const deleteBtn = document.getElementById("deleteButton");
    const addBtn = document.getElementById("addButton");

    const editableFields = ["modalContactNumber", "modalEmail", "modalAddress"];
    const allFields = [
        "modalOwnerName",
        "modalContactNumber",
        "modalEmail",
        "modalDate",
        "modalAddress",
        "modalPetType",
        "modalBreed",
        "modalGender",
        "modalBirthday",
        "modalMarkings",
        "modalDisease"
    ];

    const historyEl = document.getElementById("modalHistory");

    if (!isEditing) {
        // Enable only contact number and address
        editableFields.forEach(id => {
            const el = document.getElementById(id);
            el.setAttribute("contenteditable", true);
        });

        historyEl.setAttribute("readonly", true); // Keep history readonly

        // Update buttons
        editBtn.textContent = "SAVE";
        editBtn.classList.replace("bg-blue-600", "bg-sky-500");

        deleteBtn.textContent = "CANCEL";
        deleteBtn.classList.replace("bg-red-400", "bg-red-300");
        deleteBtn.onclick = cancelEdit;

        addBtn.classList.remove("hidden");

        isEditing = true;
    } else {
        // Save changes
        let clients = JSON.parse(localStorage.getItem("clients")) || [];
        const client = clients[window.clientIndex];

        client.contactNumber = document.getElementById("modalContactNumber").textContent;
        client.email = document.getElementById("modalEmail").textContent;
        client.address = document.getElementById("modalAddress").textContent;

        localStorage.setItem("clients", JSON.stringify(clients));

        // Disable fields again
        editableFields.forEach(id => {
            const el = document.getElementById(id);
            el.setAttribute("contenteditable", false);
        });

        // Reset buttons
        editBtn.textContent = "EDIT";
        editBtn.classList.replace("bg-green-500", "bg-green-500");

        deleteBtn.textContent = "DELETE";
        deleteBtn.classList.replace("bg-red-500", "bg-red-400");
        deleteBtn.onclick = deleteClient;

        addBtn.classList.add("hidden");

        isEditing = false;

        loadClientData();
        alert("Client information updated successfully.");
        
    }
}

function cancelEdit() {
    const clients = JSON.parse(localStorage.getItem("clients")) || [];
    const client = clients[window.clientIndex];
    showClientDetails(client, window.clientIndex); // Resets modal view

    // Reset state
    const editBtn = document.getElementById("editButton");
    const deleteBtn = document.getElementById("deleteButton");
    const addBtn = document.getElementById("addButton");

    editBtn.textContent = "EDIT";
    editBtn.classList.replace("bg-green-500", "bg-green-400");

    deleteBtn.textContent = "DELETE";
    deleteBtn.classList.replace("bg-red-500", "bg-red-400");
    deleteBtn.onclick = deleteClient;

    addBtn.classList.add("hidden");

    isEditing = false;
}

function deleteClient() {
    let clients = JSON.parse(localStorage.getItem("clients")) || [];
    clients.splice(window.clientIndex, 1);
    localStorage.setItem("clients", JSON.stringify(clients));
    closeModal();
    loadClientData();
}

function closeModal() {
    document.getElementById("clientModal").classList.add("hidden");

    // Reset the buttons to their default state
    const editBtn = document.getElementById("editButton");
    const deleteBtn = document.getElementById("deleteButton");
    const addBtn = document.getElementById("addButton");

    // Reset button text and classes
    editBtn.textContent = "EDIT";
    editBtn.classList.replace("bg-green-500", "bg-green-400");

    deleteBtn.textContent = "DELETE";
    deleteBtn.classList.replace("bg-red-500", "bg-red-400");
    deleteBtn.onclick = deleteClient;

    // Hide the add button
    addBtn.classList.add("hidden");

    // Reset editing state
    isEditing = false;
}

window.onload = function () {
    loadClientData();
};