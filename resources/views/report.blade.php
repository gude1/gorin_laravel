<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Utilization Report</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
</head>

<body class="p-4">

    <div class="container">
        <h4 class="mb-4">ITEM UTILIZATION REPORT VIEW</h4>

        <!-- Report Form -->
        <form id="reportForm">
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Start Date</label>
                <div class="col-sm-4">
                    <input type="text" id="startDate" class="form-control datepicker" placeholder="Select Start Date"
                        autocomplete="off">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">End Date</label>
                <div class="col-sm-4">
                    <input type="text" id="endDate" class="form-control datepicker" placeholder="Select End Date"
                        autocomplete="off">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Items</label>
                <div class="col-sm-4">
                    <select id="items" class="form-control" multiple></select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Generate Report</button>
        </form>

        <!-- Report Table -->
        <div class="mt-4">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Model / Network</th>
                        <th>Max Out of Warehouse</th>
                        <th>Avg Out of Warehouse</th>
                        <th>Total Quantity</th>
                        <th>Total Orders</th>
                        <th>Avg Qty Per Order</th>
                        <th>Max Quantity in an Order</th>
                        <th>Current Inventory</th>
                        <th>Purchase Amount</th>
                    </tr>
                </thead>
                <tbody id="reportTableBody">
                    <tr>
                        <td colspan="9" class="text-center">No data available</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Initialize Datepicker
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });

            // Fetch and populate items in multi-select dropdown
            function fetchItems() {
                $.ajax({
                    url: "/api/get-item-list", // Provide the correct URL for fetching items
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#items').empty();
                        $.each(data.data, function (index, item) {
                            $('#items').append('<option value="' + item.id + '">' + item.model + '</option>');
                        });
                    },
                    error: function () {
                        alert("Error fetching items.");
                    }
                });
            }

            fetchItems(); // Call on page load

            // Handle form submission
            $('#reportForm').submit(function (event) {
                event.preventDefault();

                let startDate = $('#startDate').val();
                let endDate = $('#endDate').val();
                let selectedItems = $('#items').val();

                if (!startDate || !endDate || selectedItems.length === 0) {
                    alert("Please select a start date, end date, and at least one item.");
                    return;
                }

                $.ajax({
                    url: "/api/get-report",  // Replace with your provided URL
                    type: "POST",
                    data: {
                        start_date: startDate,
                        end_date: endDate,
                        items: selectedItems
                    },
                    dataType: "json",
                    success: function (response) {
                        let rows = '';
                        $.each(response.data, function (index, item) {
                            rows += `
                                <tr>
                                    <td>${item.model}</td>
                                    <td>${item.max_out_of_warehouse}</td>
                                    <td>${item.avg_out_of_warehouse}</td>
                                    <td>${item.total_quantity}</td>
                                    <td>${item.total_orders}</td>
                                    <td>${item.avg_qty_per_order}</td>
                                    <td>${item.max_qty_in_order}</td>
                                    <td>${item.current_inventory}</td>
                                    <td class="${item.purchase_amount > 0 ? 'text-danger' : ''}">${item.purchase_amount}</td>
                                </tr>
                            `;
                        });

                        $('#reportTableBody').html(rows || '<tr><td colspan="9" class="text-center">No records found</td></tr>');
                    },
                    error: function () {
                        alert("Error generating report.");
                    }
                });
            });
        });
    </script>

</body>

</html>