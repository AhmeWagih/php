<?php

$database = new mysqli('localhost', 'root', 'Aa**2003//', 'PHPCourse');

if ($database->connect_error) {
  die("Connection failed: " . $database->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $first_name = trim($_POST['first_name'] ?? '');
  $last_name = trim($_POST['last_name'] ?? '');
  $department = trim($_POST['department'] ?? '');
  $country = trim($_POST['country'] ?? '');
  $gender = trim($_POST['gender'] ?? '');
  $address = trim($_POST['address'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $skills = $_POST['skills'] ?? [];
  $file = $_FILES['image'] ?? null;

  if (!is_array($skills)) {
    $skills = [$skills];
  }

  $full_name = trim($first_name . ' ' . $last_name);
  $errors = [];
  $image_name = 'default.png';

  if (empty($first_name)) {
    $errors[] = "First name is required.";
  } elseif (strlen($first_name) < 3) {
    $errors[] = "First Name Must be more than 3 letters";
  }

  if (empty($last_name)) {
    $errors[] = "Last name is required.";
  } elseif (strlen($last_name) < 3) {
    $errors[] = "Last Name must be more than 3 letters.";
  }

  if (empty($email)) {
    $errors[] = "Email is required.";
  } else {
    if (!preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $email)) {
      $errors[] = "Email is invalid (regex validation failed).";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "Email is invalid (filter_var validation failed).";
    }
  }

  if (empty($password)) {
    $errors[] = "Password is required.";
  } else {
    if (strlen($password) !== 8) {
      $errors[] = "Password must be exactly 8 characters.";
    }

    if (!preg_match('/^[A-Za-z0-9_]+$/', $password)) {
      $errors[] = "Password: No special characters allowed. Only underscore (_) is permitted.";
    }

    if (preg_match('/[A-Z]/', $password)) {
      $errors[] = "Password: No capital characters allowed.";
    }
  }

  $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
  $image_name = time() . '_' . uniqid() . '.' . $ext;
  $target_path = 'uploads/' . $image_name;

  if (!move_uploaded_file($file['tmp_name'], $target_path)) {
    $errors[] = "Failed to upload image.";
    $image_name = 'default.png';
  }

  if (!empty($errors)) {
    header("Location: index.php?errors=" . urlencode(implode(", ", $errors)));
    exit();
  }

  $skills = json_encode($_POST['skills'] ?? []);

  $stmt = $database->prepare(
    "INSERT INTO users (first_name, last_name, image, email, password, country, address, gender, department, skills)
    VALUES ('$first_name',
      '$last_name',
      '$image_name',
      '$email',
      '$password',
      '$country',
      '$address',
      '$gender',
      '$department',
      '$skills')"
  );
  $stmt->execute();

  header("Location: users.php");
  exit();

}

$users = [];
$result = $database->query(
  "SELECT id, first_name, last_name, department, country, gender, email, skills FROM users ORDER BY id DESC"
);

if ($result) {
  while ($row = $result->fetch_assoc()) {
    $users[] = $row;
  }
  $result->free();
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>All Users</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 py-8">
  <div class="max-w-6xl mx-auto px-4">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">All Users</h1>

    <div class="overflow-x-auto shadow-md rounded-lg ">
      <table class="w-full bg-white">
        <thead>
          <tr class="bg-blue-600 text-white">
            <th class="px-6 py-3 text-left text-sm font-semibold">Name</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">Department</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">Country</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">Gender</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">Email</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">Skills</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">ِActions</th>

          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $index => $user): ?>
            <?php
            $decodedSkills = json_decode($user['skills'] ?? '[]', true);
            if (!is_array($decodedSkills)) {
              $decodedSkills = [];
            }
            $skillsText = !empty($decodedSkills) ? implode(', ', $decodedSkills) : '-';
            ?>
            <tr
              class="<?php echo $index % 2 === 0 ? 'bg-gray-50' : 'bg-white'; ?> border-b border-gray-200 hover:bg-blue-50 transition">
              <td class="px-6 py-4 text-sm text-nowrap text-gray-900 font-medium">
                <?php echo (trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: '-'); ?>
              </td>
              <td class="px-6 py-4 text-sm text-nowrap text-gray-700"><?php echo ($user['department'] ?: '-'); ?></td>
              <td class="px-6 py-4 text-sm text-nowrap text-gray-700"><?php echo ($user['country'] ?: '-'); ?></td>
              <td class="px-6 py-4 text-sm text-nowrap text-gray-700"><?php echo ($user['gender'] ?: '-'); ?></td>
              <td class="px-6 py-4 text-sm text-nowrap text-gray-700"><?php echo ($user['email'] ?: '-'); ?></td>
              <td class="px-6 py-4 text-sm text-nowrap text-gray-700"><?php echo ($skillsText); ?></td>
              <td class="px-6 py-4 text-sm text-nowrap text-gray-700">
                <a href="details.php?id=<?php echo urlencode((string) ($user['id'] ?? '')); ?>"
                  class="text-indigo-600 hover:text-indigo-900">Show</a>
                <a href="delete.php?id=<?php echo urlencode((string) ($user['id'] ?? '')); ?>"
                  class="text-red-600 hover:text-red-900">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>

</html>