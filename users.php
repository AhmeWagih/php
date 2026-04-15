<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

require('connection.php');
require('./classes/User.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $httpMethod = strtoupper(trim($_POST['_method'] ?? 'POST'));
  $isUpdate = $httpMethod === 'PATCH';
  $userId = (int) ($_POST['id'] ?? 0);

  $skills = $_POST['skills'] ?? [];
  if (!is_array($skills)) {
    $skills = [$skills];
  }

  $userModel = (new User($database))
    ->setFirstName((string) ($_POST['first_name'] ?? ''))
    ->setLastName((string) ($_POST['last_name'] ?? ''))
    ->setDepartment((string) ($_POST['department'] ?? ''))
    ->setCountry((string) ($_POST['country'] ?? ''))
    ->setGender((string) ($_POST['gender'] ?? ''))
    ->setAddress((string) ($_POST['address'] ?? ''))
    ->setEmail((string) ($_POST['email'] ?? ''))
    ->setPassword((string) ($_POST['password'] ?? ''))
    ->setSkills($skills);

  $errors = $userModel->validate(true);

  // $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
  // $image_name = time() . '_' . uniqid() . '.' . $ext;
  // $target_path = 'uploads/' . $image_name;

  // if (!move_uploaded_file($file['tmp_name'], $target_path)) {
  //   $errors[] = "Failed to upload image.";
  //   $image_name = 'default.png';
  // }

  if (!empty($errors)) {
    $errorText = urlencode(implode(", ", $errors));
    if ($isUpdate && $userId > 0) {
      header("Location: edit.php?id={$userId}&errors={$errorText}");
      exit();
    }

    header("Location: index.php?errors={$errorText}");
    exit();
  }

  if ($isUpdate) {
    if ($userId <= 0) {
      header("Location: users.php");
      exit();
    }

    $userModel->updateById($userId);

    header("Location: users.php");
    exit();
  }

  $userModel->create();

  header("Location: users.php");
  exit();

}

$users = (new User($database))->findAll();

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
            <th class="px-6 py-3 text-left text-sm font-semibold">Actions</th>

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
                <a href="edit.php?id=<?php echo urlencode((string) ($user['id'] ?? '')); ?>"
                  class="text-indigo-600 p-1 rounded border border-indigo-600 hover:bg-indigo-600 hover:text-white">Edit</a>
                <a href="details.php?id=<?php echo urlencode((string) ($user['id'] ?? '')); ?>"
                  class="text-indigo-600 p-1 rounded border border-indigo-600 hover:bg-indigo-600 hover:text-white">Show</a>
                <a href="delete.php?id=<?php echo urlencode((string) ($user['id'] ?? '')); ?>"
                  class="text-red-600 border p-1 rounded border-red-600 hover:bg-red-600 hover:text-white">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>

</html>