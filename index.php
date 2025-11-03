<?php
session_start();

$formSubmitted = false;
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $formData = [
        'firstName' => htmlspecialchars(trim($_POST['firstName'] ?? '')),
        'lastName' => htmlspecialchars(trim($_POST['lastName'] ?? '')),
        'email' => filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL),
        'phone' => htmlspecialchars(trim($_POST['phone'] ?? '')),
        'dob' => htmlspecialchars(trim($_POST['dob'] ?? '')),
        'country' => htmlspecialchars(trim($_POST['country'] ?? '')),
        'gender' => htmlspecialchars(trim($_POST['gender'] ?? '')),
        'address' => htmlspecialchars(trim($_POST['address'] ?? '')),
        'qualification' => htmlspecialchars(trim($_POST['qualification'] ?? '')),
        'experience' => intval($_POST['experience'] ?? 0),
        'interests' => isset($_POST['interests']) ? array_map('htmlspecialchars', $_POST['interests']) : [],
        'comments' => htmlspecialchars(trim($_POST['comments'] ?? ''))
    ];
    
    // Validate required fields
    $errors = [];
    if (empty($formData['firstName'])) $errors[] = 'First name is required';
    if (empty($formData['lastName'])) $errors[] = 'Last name is required';
    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if (empty($formData['phone'])) $errors[] = 'Phone number is required';
    if (empty($formData['dob'])) $errors[] = 'Date of birth is required';
    if (empty($formData['country'])) $errors[] = 'Country is required';
    if (empty($formData['gender'])) $errors[] = 'Gender is required';
    if (empty($formData['address'])) $errors[] = 'Address is required';
    if (empty($formData['qualification'])) $errors[] = 'Qualification is required';
    
    if (empty($errors)) {
        $formSubmitted = true;
        
        
        
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'data' => $formData
        ];
        file_put_contents('registrations.json', json_encode($logData, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Portal</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ffb88c 0%, #ffd89b 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 900px;
            width: 100%;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .header {
            background: linear-gradient(135deg, #ffb88c 0%, #ffd89b 100%);
            padding: 40px;
            text-align: center;
            color: white;
            position: relative;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .form-section {
            padding: 40px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }

        .input-group {
            position: relative;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 0.95em;
        }

        .input-group input,
        .input-group select,
        .input-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s ease;
            background: white;
        }

        .input-group input:focus,
        .input-group select:focus,
        .input-group textarea:focus {
            outline: none;
            border-color: #ffb88c;
            box-shadow: 0 0 0 3px rgba(255, 184, 140, 0.2);
        }

        .input-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .radio-group label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-weight: normal;
        }

        .radio-group input[type="radio"] {
            width: auto;
            margin-right: 8px;
            cursor: pointer;
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-weight: normal;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 8px;
            cursor: pointer;
        }

        .btn-submit {
            background: linear-gradient(135deg, #ffb88c 0%, #ffd89b 100%);
            color: white;
            border: none;
            padding: 15px 50px;
            font-size: 1.1em;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 184, 140, 0.4);
            display: block;
            margin: 30px auto 0;
            font-weight: 600;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 184, 140, 0.6);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .results-section {
            padding: 40px;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .results-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .results-header h2 {
            color: #ff9d6e;
            font-size: 2em;
            margin-bottom: 10px;
        }

        .results-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .result-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 15px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .result-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .result-label {
            font-weight: 700;
            color: #555;
        }

        .result-value {
            color: #333;
        }

        .btn-back {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 40px;
            font-size: 1em;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
            margin: 20px auto 0;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .error {
            border-color: #ff4757 !important;
        }

        .success-icon {
            font-size: 4em;
            color: #28a745;
            text-align: center;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 1.8em;
            }
            
            .result-row {
                grid-template-columns: 1fr;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Registration Portal</h1>
            <p>Complete your profile information</p>
        </div>

        <?php if ($formSubmitted): ?>
        <div class="results-section">
            <div class="success-icon">✓</div>
            <div class="results-header">
                <h2>Registration Successful</h2>
                <p>Your information has been submitted successfully</p>
            </div>
            <div class="results-card">
                <div class="result-row">
                    <div class="result-label">Registration ID:</div>
                    <div class="result-value"><?php echo strtoupper(substr(md5(time()), 0, 8)); ?></div>
                </div>
                <div class="result-row">
                    <div class="result-label">Full Name:</div>
                    <div class="result-value"><?php echo $formData['firstName'] . ' ' . $formData['lastName']; ?></div>
                </div>
                <div class="result-row">
                    <div class="result-label">Email:</div>
                    <div class="result-value"><?php echo $formData['email']; ?></div>
                </div>
                <div class="result-row">
                    <div class="result-label">Phone:</div>
                    <div class="result-value"><?php echo $formData['phone']; ?></div>
                </div>
                <div class="result-row">
                    <div class="result-label">Date of Birth:</div>
                    <div class="result-value"><?php echo date('F d, Y', strtotime($formData['dob'])); ?></div>
                </div>
                <div class="result-row">
                    <div class="result-label">Country:</div>
                    <div class="result-value"><?php echo $formData['country']; ?></div>
                </div>
                <div class="result-row">
                    <div class="result-label">Gender:</div>
                    <div class="result-value"><?php echo $formData['gender']; ?></div>
                </div>
                <div class="result-row">
                    <div class="result-label">Address:</div>
                    <div class="result-value"><?php echo nl2br($formData['address']); ?></div>
                </div>
                <div class="result-row">
                    <div class="result-label">Qualification:</div>
                    <div class="result-value"><?php echo $formData['qualification']; ?></div>
                </div>
                <div class="result-row">
                    <div class="result-label">Experience:</div>
                    <div class="result-value"><?php echo $formData['experience']; ?> years</div>
                </div>
                <div class="result-row">
                    <div class="result-label">Areas of Interest:</div>
                    <div class="result-value">
                        <?php echo !empty($formData['interests']) ? implode(', ', $formData['interests']) : 'None selected'; ?>
                    </div>
                </div>
                <?php if (!empty($formData['comments'])): ?>
                <div class="result-row">
                    <div class="result-label">Comments:</div>
                    <div class="result-value"><?php echo nl2br($formData['comments']); ?></div>
                </div>
                <?php endif; ?>
                <div class="result-row">
                    <div class="result-label">Submission Time:</div>
                    <div class="result-value"><?php echo date('F d, Y \a\t g:i A'); ?></div>
                </div>
            </div>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn-back">Submit Another Registration</a>
        </div>
        <?php else: ?>
        <div class="form-section">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-grid">
                    <div class="input-group">
                        <label for="firstName">First Name *</label>
                        <input type="text" id="firstName" name="firstName" required>
                    </div>

                    <div class="input-group">
                        <label for="lastName">Last Name *</label>
                        <input type="text" id="lastName" name="lastName" required>
                    </div>

                    <div class="input-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="input-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>

                    <div class="input-group">
                        <label for="dob">Date of Birth *</label>
                        <input type="date" id="dob" name="dob" required>
                    </div>

                    <div class="input-group">
                        <label for="country">Country *</label>
                        <select id="country" name="country" required>
                            <option value="">Select Country</option>
                            <option value="India">India</option>
                            <option value="USA">United States</option>
                            <option value="UK">United Kingdom</option>
                            <option value="Canada">Canada</option>
                            <option value="Australia">Australia</option>
                            <option value="Germany">Germany</option>
                            <option value="France">France</option>
                        </select>
                    </div>

                    <div class="input-group full-width">
                        <label>Gender *</label>
                        <div class="radio-group">
                            <label><input type="radio" name="gender" value="Male" required> Male</label>
                            <label><input type="radio" name="gender" value="Female"> Female</label>
                            <label><input type="radio" name="gender" value="Other"> Other</label>
                        </div>
                    </div>

                    <div class="input-group full-width">
                        <label for="address">Address *</label>
                        <textarea id="address" name="address" required></textarea>
                    </div>

                    <div class="input-group">
                        <label for="qualification">Qualification *</label>
                        <select id="qualification" name="qualification" required>
                            <option value="">Select Qualification</option>
                            <option value="High School">High School</option>
                            <option value="Bachelor's Degree">Bachelor's Degree</option>
                            <option value="Master's Degree">Master's Degree</option>
                            <option value="PhD">PhD</option>
                            <option value="Diploma">Diploma</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="experience">Years of Experience</label>
                        <input type="number" id="experience" name="experience" min="0" max="50" value="0">
                    </div>

                    <div class="input-group full-width">
                        <label>Areas of Interest</label>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="interests[]" value="Technology"> Technology</label>
                            <label><input type="checkbox" name="interests[]" value="Business"> Business</label>
                            <label><input type="checkbox" name="interests[]" value="Arts"> Arts</label>
                            <label><input type="checkbox" name="interests[]" value="Science"> Science</label>
                            <label><input type="checkbox" name="interests[]" value="Sports"> Sports</label>
                            <label><input type="checkbox" name="interests[]" value="Music"> Music</label>
                        </div>
                    </div>

                    <div class="input-group full-width">
                        <label for="comments">Additional Comments</label>
                        <textarea id="comments" name="comments" rows="4"></textarea>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Submit Registration</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>