
<?php
// Assume form data is sent via POST
$data = $_POST;

// Sanitize inputs
$company_name = trim($data['company_name'] ?? '');
$company_owner = trim($data['company_owner'] ?? '');
$tin_number = trim($data['tin_number'] ?? ''); // Correctly accessing tin_number
$registration_number = trim($data['registration_number'] ?? '');
$business_type = trim($data['business_type'] ?? '');
$business_sector = trim($data['business_sector'] ?? '');
$address = trim($data['address'] ?? '');
$country = trim($data['country'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$notes = trim($data['notes'] ?? '');

// Validation
$errors = [];

// Check required fields
if (empty($company_name)) {
    $errors[] = "Company Name is required.";
}
if (empty($company_owner)) {
    $errors[] = "Company Owner is required.";
}
if (empty($tin_number)) {
    $errors[] = "TIN Number is required.";
}
if (empty($registration_number)) {
    $errors[] = "Registration Number is required.";
}
if (empty($business_type)) {
    $errors[] = "Business Type is required.";
}
if (empty($business_sector)) {
    $errors[] = "Business Sector is required.";
}
if (empty($address)) {
    $errors[] = "Address is required.";
}
if (empty($country)) {
    $errors[] = "Country is required.";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid Email Address.";
}
if (!preg_match('/^\d{10}$/', $phone)) {
    $errors[] = "Phone number must be 10 digits.";
}

// Display errors or process the form
if (!empty($errors)) {
    echo "Validation Failed: " . implode(", ", $errors);
} else {
    // Display sanitized values for debugging
    echo "Company Name: [$company_name], Owner: [$company_owner], TIN: [$tin_number]";
    // Insert into the database (Example)
    /*
    $stmt = $conn->prepare("INSERT INTO audit_clients 
        (company_name, company_owner, tin_no, registration_number, business_type, 
         business_sector, address, country, email, phone, notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", 
        $company_name, $company_owner, $tin_no, $registration_number, 
        $business_type, $business_sector, $address, $country, $email, $phone, $notes);
    $stmt->execute();
    */
    echo "Validation Successful. Client added!";
}
?>
