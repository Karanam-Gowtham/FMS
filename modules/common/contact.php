<?php
require_once '../../includes/session.php';
require_once '../../includes/csrf.php';
include_once '../../includes/connection.php'; // Include the database connection
include_once '../../includes/header.php'; // Include the header file

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    csrfValidate();
    // Get the form data
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Prepare the SQL query to insert data into the database
    $stmt = $conn->prepare("INSERT INTO contact_form (full_name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $full_name, $email, $phone, $subject, $message);

    // Execute the query and check if successful
    if ($stmt->execute()) {
        echo "<script>alert('Your message has been successfully submitted!');</script>";
    } else {
        echo "<script>alert('Failed to submit your message. Please try again later.');</script>";
    }

    // Close the statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechCorp - Contact Us</title>
    <link rel="stylesheet" href="../../assets/../../assets/css/contact.css">
</head>
<body>
    <main class="hero">
        <div class="container">
            <div class="contact-wrapper">
                <div class="contact-info">
                    <h2>Get in Touch</h2>
                    
                    <div class="info-section">
                        <h3>Contact Details</h3>
                        <div class="info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                            <span>+91 9949784378</span>
                        </div>
                        <div class="info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                            <span>gmrit.edu.in</span>
                        </div>
                        <div class="info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span>GMRIT Rajam<br>Vizianagaram, AP 532127</span>
                        </div>
                    </div>

                    <div class="social-links">
                        <h3>Connect With Us</h3>
                        <div class="social-icons">
                            <a href="https://wa.me/9949784378" target="_blank" class="social-icon whatsapp">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                                </svg>
                            </a>
                            <a href="https://instagram.com/gmrit_rajam" target="_blank" class="social-icon instagram">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                    <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
                                </svg>
                            </a>
                            <a href="https://linkedin.com/company/" target="_blank" class="social-icon linkedin">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                                    <rect width="4" height="12" x="2" y="9"/>
                                    <circle cx="4" cy="4" r="2"/>
                                </svg>
                            </a>
                            <a href="https://gmrit.edu.in" target="_blank" class="social-icon website">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="2" x2="22" y1="12" y2="12"/>
                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="contact-form">
                    <h2>Send us a Message</h2>
                    <form action="" method="POST" id="contactForm">
                        <?php echo csrfField(); ?>
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" name="full_name" id="full_name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" name="email" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" name="phone" id="phone" pattern="^\d{10}$" title="Phone number must be 10 digits long" required>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" name="subject" id="subject" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea name="message" id="message" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-outline">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
