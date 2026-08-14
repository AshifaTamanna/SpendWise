const API = "../controllers/transaction-process.php";

const transactionList =
    document.getElementById("transactionList");

const modal =
    document.getElementById("transactionModal");

const openModalBtn =
    document.getElementById("openModal");

const closeModalBtn =
    document.getElementById("closeModal");

const cancelModalBtn =
    document.getElementById("cancelModal");

const transactionForm =
    document.getElementById("transactionForm");

const modalTitle =
    document.getElementById("modalTitle");

const transactionId =
    document.getElementById("transactionId");

const titleInput =
    document.getElementById("title");

const amountInput =
    document.getElementById("amount");

const typeInput =
    document.getElementById("type");

const categoryInput =
    document.getElementById("category");


let currentFilter = "all";


/* =========================
   LOAD TRANSACTIONS
========================= */

async function loadTransactions(filter = "all") {

    transactionList.innerHTML =
        `<div class="loading">
            Loading transactions...
        </div>`;

    try {

        const response = await fetch(
            `${API}?filter=${filter}`
        );

        const data = await response.json();

        if (!data.success) {

            transactionList.innerHTML =
                `<div class="empty">
                    ${data.message}
                </div>`;

            return;
        }

        displayTransactions(
            data.transactions
        );

    } catch (error) {

        transactionList.innerHTML =
            `<div class="empty">
                Failed to load transactions.
            </div>`;

        console.error(error);
    }
}


/* =========================
   DISPLAY
========================= */

function displayTransactions(
    transactions
) {

    if (
        !transactions ||
        transactions.length === 0
    ) {

        transactionList.innerHTML =
            `<div class="empty">
                No transactions found.
            </div>`;

        return;
    }


    transactionList.innerHTML = "";


    transactions.forEach(transaction => {

        const row =
            document.createElement("div");

        row.className =
            "transaction-row";


        const amount =
            parseFloat(
                transaction.amount
            ).toFixed(2);


        const amountClass =
            transaction.type === "income"
                ? "income"
                : "expense";


        const sign =
            transaction.type === "income"
                ? "+"
                : "-";


        const date =
            formatDate(
                transaction.created_at
            );


        row.innerHTML = `

            <div class="description">

                <i class="fa-solid fa-receipt"></i>

                <span>
                    ${escapeHTML(
                        transaction.title
                    )}
                </span>

            </div>


            <div>
                ${escapeHTML(
                    transaction.category
                )}
            </div>


            <div>

                <span class="type ${amountClass}">

                    ${capitalize(
                        transaction.type
                    )}

                </span>

            </div>


            <div class="amount ${amountClass}">

                ${sign} ৳${amount}

            </div>


            <div>

                ${date}

            </div>


            <div class="actions">

                <button
                    class="edit-btn"
                    onclick="editTransaction(
                        ${transaction.id}
                    )"
                >

                    <i class="fa-solid fa-pen"></i>

                </button>


                <button
                    class="delete-btn"
                    onclick="deleteTransaction(
                        ${transaction.id}
                    )"
                >

                    <i class="fa-solid fa-trash"></i>

                </button>

            </div>

        `;


        transactionList.appendChild(row);

    });
}


/* =========================
   ADD / UPDATE
========================= */

transactionForm.addEventListener(
    "submit",
    async function (event) {

        event.preventDefault();


        const id =
            transactionId.value;


        const title =
            titleInput.value.trim();


        const amount =
            amountInput.value;


        const type =
            typeInput.value;


        const category =
            categoryInput.value;


        if (
            !title ||
            !amount ||
            !type ||
            !category
        ) {

            alert(
                "Please fill in all fields."
            );

            return;
        }


        const action =
            id ? "update" : "add";


        try {

            const response =
                await fetch(API, {

                    method: "POST",

                    headers: {
                        "Content-Type":
                            "application/json"
                    },

                    body: JSON.stringify({

                        action: action,

                        id: id,

                        title: title,

                        amount: amount,

                        type: type,

                        category: category

                    })

                });


            const data =
                await response.json();


            if (!data.success) {

                alert(data.message);

                return;
            }


            closeModal();

            resetForm();

            loadTransactions(
                currentFilter
            );


        } catch (error) {

            console.error(error);

            alert(
                "Something went wrong."
            );

        }

    }
);


/* =========================
   EDIT
========================= */

async function editTransaction(id) {

    try {

        const response =
            await fetch(
                `${API}?filter=${currentFilter}`
            );


        const data =
            await response.json();


        if (!data.success) {

            alert(data.message);

            return;
        }


        const transaction =
            data.transactions.find(
                item =>
                    Number(item.id) ===
                    Number(id)
            );


        if (!transaction) {

            alert(
                "Transaction not found."
            );

            return;
        }


        transactionId.value =
            transaction.id;


        titleInput.value =
            transaction.title;


        amountInput.value =
            transaction.amount;


        typeInput.value =
            transaction.type;


        categoryInput.value =
            transaction.category;


        modalTitle.textContent =
            "Edit Transaction";


        openModal();


    } catch (error) {

        console.error(error);

        alert(
            "Unable to load transaction."
        );
    }
}


/* =========================
   DELETE
========================= */

async function deleteTransaction(id) {

    const confirmDelete =
        confirm(
            "Are you sure you want to delete this transaction?"
        );


    if (!confirmDelete) {
        return;
    }


    try {

        const response =
            await fetch(API, {

                method: "POST",

                headers: {
                    "Content-Type":
                        "application/json"
                },

                body: JSON.stringify({

                    action: "delete",

                    id: id

                })

            });


        const data =
            await response.json();


        if (!data.success) {

            alert(data.message);

            return;
        }


        loadTransactions(
            currentFilter
        );


    } catch (error) {

        console.error(error);

        alert(
            "Unable to delete transaction."
        );
    }
}


/* =========================
   FILTER
========================= */

document
    .querySelectorAll(".filter-btn")
    .forEach(button => {

        button.addEventListener(
            "click",
            function () {

                document
                    .querySelectorAll(
                        ".filter-btn"
                    )
                    .forEach(btn =>
                        btn.classList.remove(
                            "active"
                        )
                    );


                this.classList.add(
                    "active"
                );


                currentFilter =
                    this.dataset.filter;


                loadTransactions(
                    currentFilter
                );

            }
        );

    });


/* =========================
   MODAL
========================= */

openModalBtn.addEventListener(
    "click",
    function () {

        resetForm();

        openModal();

    }
);


closeModalBtn.addEventListener(
    "click",
    closeModal
);


cancelModalBtn.addEventListener(
    "click",
    closeModal
);


modal.addEventListener(
    "click",
    function (event) {

        if (event.target === modal) {
            closeModal();
        }

    }
);


function openModal() {

    modal.classList.add("show");

}


function closeModal() {

    modal.classList.remove("show");

}


function resetForm() {

    transactionForm.reset();

    transactionId.value = "";

    modalTitle.textContent =
        "Add Transaction";

}


/* =========================
   HELPERS
========================= */

function capitalize(value) {

    if (!value) {
        return "";
    }

    return value
        .charAt(0)
        .toUpperCase() +
        value.slice(1);
}


function formatDate(dateString) {

    const date =
        new Date(
            dateString
        );


    return date.toLocaleDateString(
        "en-GB",
        {
            day: "2-digit",
            month: "short",
            year: "numeric"
        }
    );

}


function escapeHTML(value) {

    const div =
        document.createElement("div");

    div.textContent =
        value ?? "";

    return div.innerHTML;

}


/* =========================
   INITIAL LOAD
========================= */

loadTransactions();