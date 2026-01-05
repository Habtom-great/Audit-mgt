<?php
include('header.php'); 
require_once 'db.php'; 

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit References</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 30px;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        .card-body ul {
            margin: 0;
            padding: 0;
            list-style-type: none;
        }
        .card-body ul li {
            margin-bottom: 10px;
        }
        footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Audit References</h1>

        <!-- ISA Standards Section -->
        <div class="card">
            <div class="card-header">
                International Standards on Auditing (ISAs)
            </div>
            <div class="card-body">
                <ul>
                    <li><strong>ISA 200:</strong> Overall Objectives of the Independent Auditor and the Conduct of an Audit in Accordance with ISAs</li>
                    <li><strong>ISA 240:</strong> The Auditor’s Responsibilities Relating to Fraud in an Audit of Financial Statements</li>
                    <li><strong>ISA 315:</strong> Identifying and Assessing the Risks of Material Misstatement Through Understanding the Entity and Its Environment</li>
                    <li><strong>ISA 500:</strong> Audit Evidence</li>
                    <li><strong>ISA 520:</strong> Analytical Procedures</li>
                    <li><strong>ISA 540:</strong> Auditing Accounting Estimates and Related Disclosures</li>
                    <li><strong>ISA 600:</strong> Special Considerations—Audits of Group Financial Statements (Including the Work of Component Auditors)</li>
                    <li><strong>ISA 700:</strong> Forming an Opinion and Reporting on Financial Statements</li>
                </ul>
            </div>
        </div>

        <!-- Practical Cases Section -->
        <div class="card">
            <div class="card-header">
                Practical Cases in Auditing
            </div>
            <div class="card-body">
                <ul>
                    <li><strong>Enron Scandal (2001):</strong> A landmark case that highlights the importance of assessing fraud risks and ethical responsibilities.</li>
                    <li><strong>WorldCom Fraud (2002):</strong> Demonstrates the significance of ISA 240 in detecting and addressing fraudulent financial reporting.</li>
                    <li><strong>Satyam Scandal (2009):</strong> Highlights the need for robust audit evidence (ISA 500) and the auditor’s role in verifying accounting records.</li>
                    <li><strong>Lehman Brothers (2008):</strong> Emphasizes the use of analytical procedures (ISA 520) in identifying red flags in financial reporting.</li>
                    <li><strong>Volkswagen Emissions Scandal (2015):</strong> A case that underscores the importance of assessing risks in complex entities (ISA 315).</li>
                </ul>
            </div>
        </div>

        <!-- Additional Resources Section -->
        <div class="card">
            <div class="card-header">
                Additional Resources
            </div>
            <div class="card-body">
                <ul>
                    <li><a href="https://www.ifac.org/standards/auditing" target="_blank">International Federation of Accountants (IFAC) - Auditing Standards</a></li>
                    <li><a href="https://www.icai.org/post.html?post_id=5777" target="_blank">Institute of Chartered Accountants of India (ICAI) - Auditing Guidance</a></li>
                    <li><a href="https://www.accaglobal.com/" target="_blank">Association of Chartered Certified Accountants (ACCA)</a></li>
                    <li><a href="https://www.aicpa.org/research/standards/auditattest.html" target="_blank">American Institute of CPAs (AICPA) - Audit and Attest Standards</a></li>
                </ul>
            </div>
        </div>
    </div>

   

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include('footer.php'); ?>