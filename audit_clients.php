<?php
include('header.php');
include('db.php');

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle search query
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$allowed_sort_columns = ['company_name', 'tin_no']; // Allowed columns for sorting
$sort_by = isset($_GET['sort_by']) && in_array($_GET['sort_by'], $allowed_sort_columns) ? $_GET['sort_by'] : 'company_name'; // Default sort

// Fetch clients with search and sorting
$sql = "SELECT * FROM audit_clients WHERE company_name LIKE ? OR tin_no LIKE ? ORDER BY $sort_by ASC";
$stmt = $conn->prepare($sql);

// Check if preparation was successful
if (!$stmt) {
    die("SQL preparation failed: " . $conn->error);
}

$search_term = "%$search%";
$stmt->bind_param("ss", $search_term, $search_term);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Clients List</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your styles -->
</head>
<body>
    <div class="container">
        <h1>Audit Clients</h1>

        <!-- Search and Sort -->
        <form method="GET" action="" class="mb-3">
            <input type="text" name="search" placeholder="Search by company name or TIN number" value="<?php echo htmlspecialchars($search); ?>" />
            <select name="sort_by">
                <option value="company_name" <?php echo $sort_by == 'company_name' ? 'selected' : ''; ?>>Sort by Company Name</option>
                <option value="tin_no" <?php echo $sort_by == 'tin_no' ? 'selected' : ''; ?>>Sort by TIN Number</option>
            </select>
            <button type="submit">Search</button>
        </form>
   <!-- Add Client Button -->
   <div class="text-center mt-4">
            <a href="add_client.php" class="btn btn-success btn-lg">
                <i class="bi bi-plus-circle"></i> Add New Client
            </a>
        </div>
    </div>
        <!-- Display success message -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <p class="success-message">Client added successfully!</p>
        <?php endif; ?>

        <!-- Clients Table -->
        <table border="1" cellpadding="10" cellspacing="0" class="table table-striped">
            <thead>
                <tr>
                    <th>Order No</th>
                    <th>Company Name</th>
                    <th>Owner</th>
                    <th>TIN No</th>
                    <th>Registration Number</th>
                    <th>Business Type</th>
                    <th>Sector</th>
                    <th>Address</th>
                    <th>Country</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Notes</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result && $result->num_rows > 0): 
                    $order_no = 1; // Initialize order number
                    while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $order_no++; ?></td>
                            <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['company_owner']); ?></td>
                            <td><?php echo htmlspecialchars($row['tin_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['registration_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['business_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['business_sector']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td><?php echo htmlspecialchars($row['country']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['notes']); ?></td>
                            <td>
                                <?php if (!empty($row['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Client Image" width="50">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="view_client.php?id=<?php echo urlencode($row['id']); ?>" class="btn btn-info btn-sm">View</a>
                                <a href="edit_client.php?id=<?php echo urlencode($row['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_client.php?id=<?php echo urlencode($row['id']); ?>" 
                                   onclick="return confirm('Are you sure you want to delete this client?');" 
                                   class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; 
                else: ?>
                    <tr>
                        <td colspan="14">No clients found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

     

    <?php include('footer.php'); ?>
</body>
</html>
