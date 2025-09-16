<?php
include 'includes/db.php';
include 'includes/header.php';


$name = $email = $phone = $message = '';
$success = $error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $message = trim($_POST['message']);
    
    
    if (empty($name) || empty($email) || empty($phone) || empty($message)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
      
        $cleanedPhone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($cleanedPhone) !== 10 || !preg_match('/^0(7[0-9]|1[0-9])/', $cleanedPhone)) {
            $error = 'Please enter a valid Sri Lankan phone number (10 digits starting with 07 or 01).';
        } else {
            try {
               
                $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $cleanedPhone, $message]);
                
              
                $success = 'Your message has been sent successfully!';
                
            
                $name = $email = $phone = $message = '';
                
            } catch (PDOException $e) {
                $error = 'There was a problem submitting your message. Please try again.';
            }
        }
    }
}
?>

<div class="container">
    <div class="page-header">
        <h1>Contact Us</h1>
        <p>Get in touch with the TechNova Team</p>
    </div>

    <div class="contact-content">
        <div class="contact-form">
            <h2>Send us a Message</h2>
            
            <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form action="contact.php" method="POST" id="contactForm">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" placeholder="Your Name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="user@gmail.com" required>
                    <small id="emailError" class="error-message" style="color: red; display: none;">Please enter a valid email address</small>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number (Sri Lankan)</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" placeholder="(076) 567 3456" required>
                    <small id="phoneError" class="error-message" style="color: red; display: none;">Please enter a valid 10-digit Sri Lankan phone number</small>
                </div>
                
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" placeholder="Enter your message here" required><?php echo htmlspecialchars($message); ?></textarea>
                </div>
                
                <button type="submit" class="btn">Send Message</button>
            </form>
        </div>
        
        <div class="contact-info">
            <h2>Other Ways to Reach Us</h2>
            
            <div class="info-item">
                <h3>Email</h3>
                <p>info@sdkaluwitharana.lk</p>
            </div>
            
            <div class="info-item">
                <h3>Phone</h3>
                <p>+94 (11) 234 5678</p>
            </div>
            
            <div class="info-item">
                <h3>Social Media</h3>
                <div class="social-links">
                    <a href="twitter.com" class="social-link">Twitter</a>
                    <a href="facebook.com" class="social-link">Facebook</a>
                    <a href="instagram.com" class="social-link">Instagram</a>
                    <a href="linkedin.com" class="social-link">LinkedIn</a>
                </div>
            </div>
            
            <div class="info-item">
                <h3>Address</h3>
                <p>NSBM Green University<br>Homagama<br>Sri Lanka</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    const emailInput = document.getElementById('email');
    const phoneError = document.getElementById('phoneError');
    const emailError = document.getElementById('emailError');
    const form = document.getElementById('contactForm');
    
  
    phoneInput.addEventListener('input', function(e) {
        const input = e.target.value.replace(/\D/g, '').substring(0, 10);
        let formattedInput = '';
        
        if (input.length > 0) {
            formattedInput = '(' + input.substring(0, 3);
            if (input.length > 3) {
                formattedInput += ') ' + input.substring(3, 6);
            }
            if (input.length > 6) {
                formattedInput += ' ' + input.substring(6, 10);
            }
        }
        
        e.target.value = formattedInput;
        
       
        if (input.length === 10 && /^0(7[0-9]|1[0-9])/.test(input)) {
            phoneError.style.display = 'none';
            phoneInput.style.borderColor = '';
        } else if (input.length > 0) {
            phoneError.style.display = 'block';
            phoneInput.style.borderColor = 'red';
        } else {
            phoneError.style.display = 'none';
            phoneInput.style.borderColor = '';
        }
    });
    
    
    emailInput.addEventListener('blur', function() {
        const email = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
            emailError.style.display = 'block';
            emailInput.style.borderColor = 'red';
        } else {
            emailError.style.display = 'none';
            emailInput.style.borderColor = '';
        }
    });
    
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        
        const phoneDigits = phoneInput.value.replace(/\D/g, '');
        if (phoneDigits.length !== 10 || !/^0(7[0-9]|1[0-9])/.test(phoneDigits)) {
            phoneError.style.display = 'block';
            phoneInput.style.borderColor = 'red';
            isValid = false;
        }
        
      
        const email = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            emailError.style.display = 'block';
            emailInput.style.borderColor = 'red';
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            // Scroll to the first error
            const firstError = document.querySelector('.error-message[style*="display: block"]');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
