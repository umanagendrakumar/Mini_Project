<?php

include 'connection.php';
// Initialize variables
$error = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rollno = strtoupper(trim($_POST['rollno'])); // Get roll number and convert to uppercase
    $password = trim($_POST['password']); // Get password

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM student_info WHERE rollno = ?");
    $stmt->bind_param("s", $rollno); // 's' means rollno is a string

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password']; // Get the hashed password from the database

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, proceed to home page
            $_SESSION['username'] = $rollno; // Store username in session
            header("Location: home.html"); // Redirect to home page
            exit();
        } else {
            // Password is incorrect
            $error = "Invalid Roll No or Password.";
        }
    } else {
        // User does not exist
        $error = "Invalid Roll No or Password.";
    }

    // Close statement
    $stmt->close();
}
// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Stylish Login Page</title>
    <style>
        /* Add your CSS styles here */
        /* Reset some default styles */
      body,
      html {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      }

      /* Apply box-sizing globally */
      *,
      *::before,
      *::after {
        box-sizing: border-box;
      }

      /* Background styling */
      body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
      }

      /* Container styling */
      .container {
        background-color: #ffffff;
        padding: 30px;
        width: 400px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        text-align: center;
        transition: box-shadow 0.3s ease-in-out;
      }

      /* Hover effect for the container */
      .container:hover {
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
      }

      /* Form header */
      h2 {
        margin-bottom: 20px;
        font-size: 24px;
        color: #333;
      }

      /* Input labels */
      label {
        display: block;
        text-align: left;
        font-size: 14px;
        margin-bottom: 5px;
        color: #666;
      }

      /* Input fields styling */
      input[type="text"],
      input[type="password"],
      button {
        width: 100%; /* Make input and button width same */
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s ease;
      }

      /* Remove default margin that may cause misalignment */
      input[type="text"],
      input[type="password"],
      button {
        margin-left: 0;
        margin-right: 0;
      }

      /* Focus state for input fields */
      input[type="text"]:focus,
      input[type="password"]:focus {
        border-color: #6e8efb;
        outline: none;
      }

      /* Button styling */
      button {
        background-color: #6e8efb;
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
      }

      /* Button hover effect */
      button:hover {
        background-color: #a777e3;
      }

      /* Text at the bottom */
      p {
        margin-top: 20px;
        font-size: 14px;
        color: #555;
      }

      /* Links */
      a {
        color: #6e8efb;
        text-decoration: none;
        font-weight: bold;
        transition: color 0.3s ease;
      }

      /* Link hover effect */
      a:hover {
        color: #a777e3;
      }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome Back</h2>
        <form action="login.php" method="POST">
            <label for="login-rollno">Roll No:</label>
            <input type="text" id="login-rollno" placeholder="Enter Roll No" name="rollno" required oninput="convertToUppercase(this)" />
            <label for="login-password">Password:</label>
            <input type="password" id="login-password" name="password" placeholder="Enter Password" required />
            <button name="login">Login</button>
        </form>
        <p style="color:red;"><?php echo $error; ?></p> <!-- Display error message if any -->
        <p>New user? <a href="register.php">Register here</a></p>
    </div>

    <script>
        // Function to convert the Roll No to uppercase as you type
        function convertToUppercase(input) {
            input.value = input.value.toUpperCase();
        }
    </script>
</body>
</html>

