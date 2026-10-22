import 'bootstrap';

const csrf = document.querySelector('meta[name="csrf-token"]').content;
const form = document.getElementById('product-form');
const body = document.getElementById('products-body');
const errorBox = document.getElementById('form-errors');
const submitBtn = document.getElementById('submit-btn');
const cancelBtn = document.getElementById('cancel-btn');
const formTitle = document.getElementById('form-title');
let products = [];
const fields = {
    id: document.getElementById('product-id'),
    name: document.getElementById('name'),
    quantity: document.getElementById('quantity'),
    price: document.getElementById('price'),
};

async function request(url, method = 'GET', payload = null) {
    const response = await fetch(url, {
        method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
        },
        body: payload ? JSON.stringify(payload) : null,
    });
    const data = await response.json().catch(() => ({}));
    if (!response.ok) {
        const error = new Error(data.message ?? 'Request failed');
        error.errors = data.errors ?? {};
        throw error;
    }
    return data;
}

function renderTable(data) {
    products = data.products;
    body.replaceChildren();
    for (const productItem of products) {
        const tr = document.createElement('tr');
        const cells = [productItem.name, productItem.quantity, productItem.submitted_at, productItem.total.toFixed(2)];
        cells.forEach((text, i) => {
            const td = document.createElement('td');
            td.textContent = text;
            if (i === 3) {
                td.className = 'text-end';
            }
            tr.appendChild(td);
        });
        const actions = document.createElement('td');
        actions.className = 'text-end';
        const edit = document.createElement('button');
        edit.type = 'button';
        edit.className = 'btn btn-primary';
        edit.textContent = 'Edit';
        edit.addEventListener('click', () => startEdit(productItem));
        actions.appendChild(edit);
        tr.appendChild(actions);
        body.appendChild(tr);
    }
    document.getElementById('grand-total').textContent = data.grand_total.toFixed(2);
}

function showErrors(error) {
    const messages = Object.values(error.errors).flat();
    errorBox.replaceChildren(...(messages.length ? messages : [error.message]).map((message) => {
        const div = document.createElement('div');
        div.textContent = message;
        return div;
    }));
    errorBox.classList.remove('d-none');
}

function resetForm() {
    form.reset();
    fields.id.value = '';
    errorBox.classList.add('d-none');
    formTitle.textContent = 'Add product';
    submitBtn.textContent = 'Submit';
    cancelBtn.classList.add('d-none');
}

function startEdit(data) {
    fields.id.value = data.id;
    fields.name.value = data.name;
    fields.quantity.value = data.quantity;
    fields.price.value = data.price;
    errorBox.classList.add('d-none');
    formTitle.textContent = 'Edit product';
    submitBtn.textContent = 'Save changes';
    cancelBtn.classList.remove('d-none');
    fields.name.focus();
}

form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const id = fields.id.value;
    const payload = {
        name: fields.name.value.trim(),
        quantity: fields.quantity.value.trim(),
        price: fields.price.value.trim(),
    };
    try {
        const data = id
            ? await request(`/products/${id}`, 'PUT', payload)
            : await request('/products', 'POST', payload);
        renderTable(data);
        resetForm();
    } catch (error) {
        showErrors(error);
    }
});
cancelBtn.addEventListener('click', resetForm);
request('/products').then(renderTable).catch(showErrors);
