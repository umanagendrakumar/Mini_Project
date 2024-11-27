<?php

include 'connection.php';

// Handle registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rollno = strtoupper(trim($_POST['rollno'])); // Get roll number and convert to uppercase
    $password = trim($_POST['password']); // Get password

    // Hash the password using password_hash()
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into student_info table
    $query = "INSERT INTO student_info (rollno, password) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $rollno, $hashed_password);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Registration successful, redirect to login page
        header("Location: login.php");
        exit();
    } else {
        // Registration failed, display error message
        $error = "Registration failed. Please try again.";
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>

<!-- Registration form HTML goes here -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adding to Database</title>
    <style>
        /* Body and Container Styling */
        body {
            background-color: #f4f7fc;
            font-family: 'Arial', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100dvh;
            margin: 0;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-sizing: border-box;
        }

        h2 {
            font-size: 24px;
            font-weight: bolder;
            margin-bottom: 20px;
            color: #6e8efb;
        }

        label {
            font-size: 16px;
            color: #555;
            margin-bottom: 5px;
            display: block;
            text-align: left;
        }

        select, input, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px; /* Adjusted margin for better spacing */
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            background-color: #f9f9f9;
            transition: background-color 0.3s ease, border-color 0.3s ease;
            font-family: inherit;
            box-sizing: border-box;
        }

        input[type="password"] {
            width: 100%; /* Ensure it takes up the full width */
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        select:focus, input:focus, button:focus {
            outline: none;
            background-color: #e7f4ff;
            border-color: #6c8efc;
        }

        button {
            background-color: #6c8efc;
            color: white;
            border: none;
            cursor: pointer;
            margin: 5px 0;
            transition: background-color 0.3s ease;
        }

        button:disabled {
            background-color: #ddd;
            cursor: not-allowed;
        }

        button:hover:not(:disabled) {
            background-color: #5a77e6;
        }

        .error {
            color: red;
            font-size: 14px;
            display: none;
        }

    </style>
</head>
<body>

    <div class="form-container">
        <h2>Registration</h2>

        <form action="register.php" method="POST" class="select-container">
            <!-- Academic Year Dropdown -->
            <label for="academicYear">Academic Year:</label>
            <select id="academicYear" onchange="handleAcademicYearChange()">
                <option value="">Select Academic Year</option>
                <option value="2021-2025">2021-2025</option>
                <option value="2022-2026">2022-2026</option>
                <option value="2023-2027">2023-2027</option>
                <option value="2024-2028">2024-2028</option>
            </select>

            <!-- Branch Dropdown -->
            <label for="branch">Branch:</label>
            <select id="branch" onchange="handleBranchChange()" disabled>
                <option value="">Select Branch</option>
            </select>

            <!-- Rollno Dropdown -->
            <label for="rollno">Rollno:</label>
            <select id="rollno" name="rollno" onchange="handleRollNoChange()" disabled>
                <option value="" >Select Rollno</option>
            </select>

            <!-- Password Input -->
            <label for="password">Password:</label>
            <input type="password" id="password" placeholder="Enter Password"  disabled>

            <!-- Confirm Password Input -->
            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" placeholder="Confirm Password" name="password" disabled>

            <!-- <div id="passwordError" class="error">Password does not match.</div>
            <div id="passwordLengthError" class="error">Password length must be between 8 and 20 characters.</div> -->
            <button id="submitButton" name="register">Register</button>
    </form>

        <!-- Submit Button -->
    </div>

    <script>
        function handleAcademicYearChange() {
            const academicYear = document.getElementById("academicYear").value;
            const branchDropdown = document.getElementById("branch");

            branchDropdown.innerHTML = '<option value="">Select Branch</option>';
            branchDropdown.disabled = !academicYear;

            const branches = (academicYear === "2021-2025") 
                ? ["CSE", "ECE", "EEE", "CIV", "MECH"] 
                : ["CSE", "DATA SCIENCE", "CYBER SECURITY", "AIML", "ECE", "EEE", "CIV", "MECH"];

            branches.forEach(branch => {
                const option = document.createElement("option");
                option.value = branch;
                option.textContent = branch;
                branchDropdown.appendChild(option);
            });

            document.getElementById("submitButton").disabled = true;
        }

        function generateIDSequence(academicYear, branch) {
            const branchCodes = {
                "CSE": "CS", "ECE": "EC", "EEE": "EE", "CIV": "CE", "MECH": "ME",
                "DATA SCIENCE": "DS", "CYBER SECURITY": "CY", "AIML": "AI"
            };
            const yearPrefix = `Y${academicYear.slice(2, 4)}`;
            const branchCode = branchCodes[branch] || branch.slice(0, 2).toUpperCase();
            const nextYearPrefix = `L${parseInt(academicYear.slice(2, 4)) + 1}`;

            const rollNos = [];
            for (let i = 1; i <= 70; i++) {
                rollNos.push(`${yearPrefix}${branchCode}32${String(i).padStart(2, '0')}`);
            }
            for (let i = 1; i <= 9; i++) {
                rollNos.push(`${yearPrefix}I${branchCode}32${String(i).padStart(2, '0')}`);
            }
            for (let i = 67; i <= 75; i++) {
                rollNos.push(`${nextYearPrefix}${branchCode}32${i}`);
            }

            if (academicYear === "2021-2025" && branch === "CSE") {
                rollNos.push("Y20CS3218");
            }

            return rollNos;
        }

        function handleBranchChange() {
            const academicYear = document.getElementById("academicYear").value;
            const branch = document.getElementById("branch").value;
            const rollNoDropdown = document.getElementById("rollno");
            rollNoDropdown.innerHTML = '<option value="">Select Rollno</option>';

            if (branch) {
                const rollNos = generateIDSequence(academicYear, branch);
                rollNos.forEach(rollNoID => {
                    const option = document.createElement("option");
                    option.value = rollNoID;
                    option.textContent = rollNoID;
                    rollNoDropdown.appendChild(option);
                });
                rollNoDropdown.disabled = false;
            } else {
                rollNoDropdown.disabled = true;
            }

            document.getElementById("submitButton").disabled = true;
        }

        function handleRollNoChange() {
            const rollNo = document.getElementById("rollno").value;
            document.getElementById("submitButton").disabled = !rollNo;

            if (rollNo) {
                document.getElementById("password").disabled = false;
                document.getElementById("confirmPassword").disabled = false;
            } else {
                document.getElementById("password").disabled = true;
                document.getElementById("confirmPassword").disabled = true;
            }

            // checkPasswordMatch();
        }

        // function checkPasswordMatch() {
        //     const password = document.getElementById("password").value;
        //     const confirmPassword = document.getElementById("confirmPassword").value;
        //     const passwordError = document.getElementById("passwordError");
        //     const passwordLengthError = document.getElementById("passwordLengthError");
        //     const submitButton = document.getElementById("submitButton");

        //     passwordError.style.display = "none";
        //     passwordLengthError.style.display = "none";

        //     let validPassword = true;

        //     if (password.length < 8 || password.length > 20) {
        //         passwordLengthError.style.display = "block";
        //         validPassword = false;
        //     }

        //     if (password !== confirmPassword && confirmPassword !== "") {
        //         passwordError.style.display = "block";
        //         validPassword = false;
        //     }

        //     submitButton.disabled = !validPassword || !confirmPassword || password !== confirmPassword || password.length < 8 || password.length > 20;
        // }

        // document.getElementById("confirmPassword").addEventListener("input", checkPasswordMatch);
        // document.getElementById("password").addEventListener("input", checkPasswordMatch);

        // function handleSubmit() {
        //     alert("Navigating to Dashboard");
        // }
    </script>

</body>
</html>
