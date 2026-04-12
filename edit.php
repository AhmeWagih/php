<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

require('connection.php');
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
  header('Location: users.php');
  exit();
}

$stmt = $database->prepare(
  'SELECT first_name, last_name, department, country, gender, address, email, password, skills, image
    FROM users
    WHERE id = ?
    LIMIT 1'
);

$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result ? $result->fetch_assoc() : null;

if (!$user) {
  header('Location: users.php');
  exit();
};


$first_name = $user['first_name'] ?? '';
$last_name = $user['last_name'] ?? '';
$department = $user['department'] ?? '';
$country = $user['country'] ?? '';
$gender = $user['gender'] ?? '';
$address = $user['address'] ?? '';
$email = $user['email'] ?? '';
$password = $user['password'] ?? '';
$skills = $user['skills'] ?? '';

if (is_string($skills)) {
  $decodedSkills = json_decode($skills, true);
  if (is_array($decodedSkills)) {
    $selectedSkills = array_values(array_filter(array_map('trim', $decodedSkills)));
  } else {
    $selectedSkills = array_values(array_filter(array_map('trim', preg_split('/[,|]/', $skills))));
  }
} elseif (is_array($skills)) {
  $selectedSkills = array_values(array_filter(array_map('trim', $skills)));
} else {
  $selectedSkills = [];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit User Form</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50">
  <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-semibold leading-tight text-slate-800 mb-3">Edit User</h1>
    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
      <form action="users.php" method="POST">
        <input type="hidden" name="_method" value="PATCH">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">First Name</label>
            <input type="text" name="first_name" placeholder="John" value="<?php echo ($first_name); ?>"
              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none ring-0 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
              required>
            
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Last Name</label>
            <input type="text" name="last_name" placeholder="Doe" value="<?php echo ($last_name); ?>"
              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none ring-0 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
              required>
            
          </div>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Department</label>
            <input type="text" name="department" placeholder="Open Source" value="<?php echo ($department); ?>"
              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none ring-0 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Country</label>
            <select name="country" value="<?php echo ($country); ?>"
              class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-500 outline-none ring-0 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
              <option <?php echo ($country === 'Egypt') ? 'selected' : ''; ?> value="Egypt">Egypt</option>
              <option <?php echo ($country === 'USA') ? 'selected' : ''; ?> value="USA">USA</option>
            </select>
          </div>
        </div>

        <div class="mt-4">
          <label class="mb-2 block text-sm font-semibold text-slate-700">Residential Address</label>
          <textarea name="address"
            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none ring-0 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
            rows="3" placeholder="Enter full street address, city, and postal code..."><?php echo ($address); ?></textarea>
        </div>

        <div class="mt-4">
          <label class="mb-2 block text-sm font-semibold text-slate-700">Gender</label>
          <div class="flex flex-wrap gap-6">
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
              <input class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500" type="radio" name="gender"
                id="male" value="male" <?php echo ($gender === 'male') ? 'checked' : ''; ?>>
              Male
            </label>
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
              <input class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500" type="radio" name="gender"
                id="female" value="female" <?php echo ($gender === 'female') ? 'checked' : ''; ?>>
              Female
            </label>
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
              <input class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500" type="radio" name="gender"
                id="other" value="other" <?php echo ($gender === 'other') ? 'checked' : ''; ?>>
              Other
            </label>
          </div>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
            <input type="email" name="email" value="<?php echo ($email); ?>"
              class="w-full rounded-lg border border-blue-100  px-3 py-2 text-sm outline-none ring-0 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
              placeholder="email@example.com">
            
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
            <div class="flex overflow-hidden rounded-lg border border-blue-100 bg-blue-50">
              <input id="pass" type="password" name="password"
                class="w-full rounded-lg border border-blue-100  px-3 py-2 text-sm outline-none ring-0 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                value="<?php echo ($password); ?>">
            </div>
            
          </div>
        </div>

        <div class="mt-4">
          <label class="mb-2 block text-sm font-semibold text-slate-700">Professional Skills</label>
          <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
              <?php
              $skills = ['HTML', 'CSS', 'JavaScript', 'PHP', 'MySQL', 'Python'];
              foreach ($skills as $skill): ?>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                  <input class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" type="checkbox"
                    name="skills[]" value="<?php echo $skill; ?>" <?php echo in_array($skill, $selectedSkills, true) ? 'checked' : ''; ?>>
                  <?php echo $skill; ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <hr class="my-6 border-slate-200">
        <div class="mt-4 flex justify-end gap-3">
          <button type="button"
            class="rounded-lg px-4 py-2 text-sm font-medium text-slate-500 transition hover:bg-slate-100">Cancel</button>
          <button type="submit"
            class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">Update
            User</button>
        </div>
      </form>
    </div>
  </div>
</body>

</html>