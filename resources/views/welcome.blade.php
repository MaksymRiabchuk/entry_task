<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Entry Task</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
<div class="container py-5">
    <div class="card mb-4">
        <div class="card-header" id="form-title">Add product</div>
        <div class="card-body">
            <div id="form-errors" class="alert alert-danger d-none"></div>
            <form id="product-form" novalidate>
                <input type="hidden" id="product-id">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="name" class="form-label">Product name</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="col-md-4">
                        <label for="quantity" class="form-label">Quantity in stock</label>
                        <input type="text" class="form-control" id="quantity" inputmode="numeric" required>
                    </div>
                    <div class="col-md-4">
                        <label for="price" class="form-label">Price per item</label>
                        <input type="text" class="form-control" id="price" inputmode="decimal" required>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success" id="submit-btn">Submit</button>
                    <button type="button" class="btn btn-secondary d-none" id="cancel-btn">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered bg-white align-middle">
            <thead>
            <tr>
                <th>Product name</th>
                <th>Quantity in stock</th>
                <th>Datetime submitted</th>
                <th class="text-end">Total value number</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody id="products-body"></tbody>
            <tfoot>
            <tr class="fw-bold">
                <td colspan="3" class="text-end">Sum</td>
                <td class="text-end" id="grand-total">0.00</td>
                <td></td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
</body>

</html>
