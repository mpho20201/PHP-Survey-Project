<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "survey_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $dob = $_POST['dob'];
    $contact_number = trim($_POST['contact_number']);
    $favorite_food = isset($_POST['favorite_food']) ? implode(',', $_POST['favorite_food']) : '';
    $movies_rating = $_POST['movies_rating'] ?? '';
    $radio_rating = $_POST['radio_rating'] ?? '';
    $eatout_rating = $_POST['eatout_rating'] ?? '';
    $tv_rating = $_POST['tv_rating'] ?? '';

    if (!$full_name) $errors[] = "Full Name is required.";
    if (!$email) $errors[] = "Email is required.";
    if (!$dob) $errors[] = "Date of Birth is required.";
    if (!$contact_number) $errors[] = "Contact Number is required.";
    if (!$favorite_food) $errors[] = "Select at least one favorite food.";
    if (!$movies_rating || !$radio_rating || !$eatout_rating || !$tv_rating) $errors[] = "Rate all activities.";

    if ($dob) {
        $age = date_diff(date_create($dob), date_create('today'))->y;
        if ($age < 5 || $age > 120) $errors[] = "Age must be between 5 and 120.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO surveys (full_name, email, dob, contact_number, favorite_food, movies_rating, radio_rating, eatout_rating, tv_rating) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssiii", $full_name, $email, $dob, $contact_number, $favorite_food, $movies_rating, $radio_rating, $eatout_rating, $tv_rating);
        $success = $stmt->execute();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fill Out Survey</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            width: 800px;
            margin: 40px auto;
            border: 2px solid #000;
            padding: 40px;
            background-color: #fff;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: center;
        }

        th {
            background: #e0e0e0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 180px auto;
            row-gap: 15px;
            column-gap: 10px;
            align-items: center;
            margin-left: 30%;
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 5px;
          
        }

        .submit-container {
            text-align: center;
            margin-top: 30px;
        }

        .submit-btn {
            background: #4da6ff;
            color: #fff;
            border: none;
            padding: 10px 30px;
            font-size: 16px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background: #007acc;
        }
    </style>
</head>
<body>
<div class="container">
    <div style="text-align:right;">
        <a href="survey_form.php">FILL OUT SURVEY</a> |
        <a href="survey_results.php">VIEW SURVEY RESULTS</a>
    </div>
    <h2>_ Surveys</h2>
    <?php
    if ($errors) foreach ($errors as $e) echo "<div class='error'>$e</div>";
    if ($success) echo "<div class='success'>Survey submitted successfully!</div>";
    ?>
    <form method="post">
        <h3>Personal Details :</h3>
        <div class="form-grid">
            <label for="full_name">Full Names:</label><br>
            <input type="text" name="full_name" id="full_name" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"><br>

            <label for="email">Email:</label><br>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"><br>

            <label for="dob">Date of Birth:</label><br>
            <input type="date" name="dob" id="dob" value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>"><br>

            <label for="contact_number">Contact Number:</label><br>
            <input type="text" name="contact_number" id="contact_number" value="<?= htmlspecialchars($_POST['contact_number'] ?? '') ?>"><br>
        </div>

        <br>
        
        <div class="checkbox-group">
        <label>What is your favorite food?</label>
            <label><input type="checkbox" name="favorite_food[]" value="Pizza" <?= in_array('Pizza', $_POST['favorite_food'] ?? []) ? 'checked' : '' ?>> Pizza</label>
            <label><input type="checkbox" name="favorite_food[]" value="Pasta" <?= in_array('Pasta', $_POST['favorite_food'] ?? []) ? 'checked' : '' ?>> Pasta</label>
            <label><input type="checkbox" name="favorite_food[]" value="Pap and Wors" <?= in_array('Pap and Wors', $_POST['favorite_food'] ?? []) ? 'checked' : '' ?>> Pap and Wors</label>
            <label><input type="checkbox" name="favorite_food[]" value="Other" <?= in_array('Other', $_POST['favorite_food'] ?? []) ? 'checked' : '' ?>> Other</label>
        </div>

        <br>
        <label>Please rate your level of agreement on a scale from 1 to 5, with 1 being "strongly agree" and 5 being "strongly disagree."</label>
        <table>
            <tr>
                <th></th>
                <th>Strongly Agree</th>
                <th>Agree</th>
                <th>Neutral</th>
                <th>Disagree</th>
                <th>Strongly Disagree</th>
            </tr>
            <?php
            $questions = [
                'movies_rating' => 'I like to watch movies',
                'radio_rating' => 'I like to listen to radio',
                'eatout_rating' => 'I like to eat out',
                'tv_rating' => 'I like to watch TV'
            ];
            foreach ($questions as $name => $label) {
                echo "<tr><td style='text-align:left;'>$label</td>";
                for ($i = 1; $i <= 5; $i++) {
                    $checked = (isset($_POST[$name]) && $_POST[$name] == $i) ? 'checked' : '';
                    echo "<td><input type='radio' name='$name' value='$i' $checked></td>";
                }
                echo "</tr>";
            }
            ?>
        </table>

        <div class="submit-container">
            <button type="submit" class="submit-btn">SUBMIT</button>
        </div>
    </form>
</div>
</body>
</html>
