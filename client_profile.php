<?php include('header.php'); ?>

<div class="container mt-5">
    <div class="row">
        <!-- Left Section: Client Information -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <img src="assets/images/default-logo.png" alt="Company Logo" class="rounded-circle mb-3" width="120">
                    <h4>Company Name: <strong>ABC Ltd.</strong></h4>
                    <p><strong>Client ID:</strong> CL123456</p>
                    <p><strong>License No:</strong> LIC-7890</p>
                    <p><strong>Registration No:</strong> REG-2024-001</p>
                    <p><strong>Business Type:</strong> Manufacturing</p>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h5>Upload Documents</h5>
                    <form action="upload_handler.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="file" class="form-label">Upload Document</label>
                            <input type="file" class="form-control" id="file" name="file" required>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm">Upload</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Section: Audit Details -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Audit Progress</h5>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 70%;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100">70%</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Audit Details</h5>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <strong>Working Paper:</strong> 
                            <a href="uploads/documents/working_paper.pdf" class="btn btn-primary btn-sm">Download</a>
                        </li>
                        <li class="list-group-item">
                            <strong>Recommendations:</strong> 
                            <a href="uploads/documents/recommendation.pdf" class="btn btn-warning btn-sm">Download</a>
                        </li>
                        <li class="list-group-item">
                            <strong>Compliance:</strong> 
                            IFRS, GAAP Standards Met
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tabs for Financial Statements -->
            <ul class="nav nav-tabs mt-4" id="financialTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="income-tab" data-bs-toggle="tab" data-bs-target="#income" type="button" role="tab" aria-controls="income" aria-selected="true">Income Statement</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="trial-tab" data-bs-toggle="tab" data-bs-target="#trial" type="button" role="tab" aria-controls="trial" aria-selected="false">Trial Balance</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="balance-tab" data-bs-toggle="tab" data-bs-target="#balance" type="button" role="tab" aria-controls="balance" aria-selected="false">Balance Sheet</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="cashflow-tab" data-bs-toggle="tab" data-bs-target="#cashflow" type="button" role="tab" aria-controls="cashflow" aria-selected="false">Cash Flow</button>
                </li>
            </ul>

            <div class="tab-content" id="financialTabsContent">
                <div class="tab-pane fade show active" id="income" role="tabpanel" aria-labelledby="income-tab">
                    <p>Revenue: $1,000,000</p>
                    <p>Expenses: $750,000</p>
                    <p>Net Income: $250,000</p>
                </div>
                <div class="tab-pane fade" id="trial" role="tabpanel" aria-labelledby="trial-tab">
                    <p>Assets: $2,000,000</p>
                    <p>Liabilities: $1,000,000</p>
                    <p>Equity: $1,000,000</p>
                </div>
                <div class="tab-pane fade" id="balance" role="tabpanel" aria-labelledby="balance-tab">
                    <p>Total Assets: $2,500,000</p>
                    <p>Total Liabilities: $1,500,000</p>
                    <p>Equity: $1,000,000</p>
                </div>
                <div class="tab-pane fade" id="cashflow" role="tabpanel" aria-labelledby="cashflow-tab">
                    <p>Operating Activities: $300,000</p>
                    <p>Investing Activities: -$150,000</p>
                    <p>Financing Activities: $50,000</p>
                    <p>Net Cash Flow: $200,000</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
