<?php

session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

require('connection.php');
require('./classes/User.php');
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
  header('Location: users.php');
  exit();
}

$userModel = new User($database);
$user = $userModel->findById($id);

if (!$user) {
  header('Location: users.php');
  exit();
}

$full_name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
$department = $user['department'] ?? '';
$country = $user['country'] ?? '';
$gender = $user['gender'] ?? '';
$address = $user['address'] ?? '';
$email = $user['email'] ?? '';
$password = $user['password'] ?? '';
$image_name = trim((string) ($user['image'] ?? ''));

$profile_image = 'https://via.placeholder.com/300x300?text=User';
if ($image_name !== '') {
  $possible_image_path = __DIR__ . '/uploads/' . $image_name;
  if (is_file($possible_image_path)) {
    $profile_image = 'uploads/' . rawurlencode($image_name);
  }
}

$decodedSkills = json_decode($user['skills'] ?? '[]', true);
if (!is_array($decodedSkills)) {
  $decodedSkills = [];
}
$skills = [];
foreach ($decodedSkills as $skill) {
  $trimmedSkill = trim((string) $skill);
  if ($trimmedSkill !== '') {
    $skills[] = $trimmedSkill;
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Details</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen text-slate-800">
  <main class="mx-auto flex min-h-screen max-w-xl items-start px-4 py-3 sm:px-6 sm:py-6">
    <section
      class="w-full rounded-[22px] bg-white/95 px-4 py-4 shadow-[0_16px_50px_rgba(99,102,241,0.12)] ring-1 ring-slate-100 sm:px-5 sm:py-5">
      <header class="mb-6 text-center flex items-center justify-between">
        <h1 class="text-3xl font-semibold tracking-tight text-indigo-500 sm:text-[2rem]">User Details</h1>
        <a href="logout.php" class="text-indigo-500 p-1 rounded border border-indigo-500 hover:text-indigo-700">Logout</a>
      </header>

      <div
        class="rounded-[20px] bg-gradient-to-b from-white to-slate-50 px-4 pb-5 pt-6 shadow-sm ring-1 ring-slate-100 sm:px-6">
        <div class="flex flex-col items-center text-center">
          <!-- <div class="relative mb-4">
            <div class="absolute inset-0 rounded-full bg-indigo-300/25 blur-[2px]"></div>
            <img src="<?php echo ($profile_image); ?>" alt="Profile avatar"
              class="relative h-36 w-36 rounded-full border-4 border-white object-cover shadow-[0_10px_30px_rgba(99,102,241,0.18)]">
          </div> -->
          <h2 class="text-[1.35rem] font-semibold tracking-tight text-slate-700 sm:text-2xl">
            <?php echo ($full_name); ?></h2>
        </div>

        <div class="mt-8 space-y-6">
          <section>
            <div
              class="mb-3 flex items-center gap-2 text-[0.72rem] font-bold uppercase tracking-[0.22em] text-slate-400">
              <span class="text-slate-500">&#128100;</span>
              Personal Information
            </div>
            <div class="rounded-2xl bg-[#f4f0ff] px-4 py-5">
              <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                  <p class="text-[0.72rem] font-medium text-slate-500">Department</p>
                  <p class="mt-1 text-[1rem] font-semibold text-slate-700"><?php echo ($department); ?></p>
                </div>
                <div>
                  <p class="text-[0.72rem] font-medium text-slate-500">Country</p>
                  <p class="mt-1 text-[1rem] font-semibold text-slate-700"><?php echo ($country); ?></p>
                </div>
                <div>
                  <p class="text-[0.72rem] font-medium text-slate-500">Gender</p>
                  <p class="mt-1 text-[1rem] font-semibold text-slate-700"><?php echo ($gender); ?></p>
                </div>
                <div>
                  <p class="text-[0.72rem] font-medium text-slate-500">Location</p>
                  <p class="mt-1 text-[1rem] font-semibold text-slate-700"><?php echo ($address); ?></p>
                </div>
              </div>
            </div>
          </section>

          <section>
            <div
              class="mb-3 flex items-center gap-2 text-[0.72rem] font-bold uppercase tracking-[0.22em] text-slate-400">
              <span class="text-slate-500">&lt;&gt;</span>
              Skills
            </div>
            <div class="flex flex-wrap gap-2">
              <?php foreach ($skills as $skill): ?>
                <span
                  class="rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600 ring-1 ring-slate-200">
                  <?php echo ($skill); ?>
                </span>
              <?php endforeach; ?>
            </div>
          </section>

          <section>
            <div
              class="mb-3 flex items-center gap-2 text-[0.72rem] font-bold uppercase tracking-[0.22em] text-slate-400">
              <span class="text-slate-500">&#128274;</span>
              Account Credentials
            </div>
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
              <div class="flex items-center gap-3 border-b border-slate-100 px-4 py-4">
                <div
                  class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-2xl text-indigo-500">@
                </div>
                <div class="min-w-0 flex-1">
                  <p class="text-xs font-medium text-slate-400">Email</p>
                  <p class="truncate text-[1rem] font-semibold text-slate-700"><?php echo ($email); ?></p>
                </div>
                <button type="button" class="rounded-lg p-2 text-indigo-500 transition hover:bg-indigo-50"
                  aria-label="Copy email">
                  &#128203;
                </button>
              </div>
              <div class="flex items-center gap-3 px-4 py-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-xl text-indigo-500">
                  &#8226;&#8226;&#8226;</div>
                <div class="min-w-0 flex-1">
                  <p class="text-xs font-medium text-slate-400">Password</p>
                  <p class="truncate text-[1rem] font-semibold tracking-[0.28em] text-slate-700">
                    <?php echo str_repeat('•', strlen((string) $password)); ?></p>
                </div>
                <button type="button" class="rounded-lg p-2 text-indigo-500 transition hover:bg-indigo-50"
                  aria-label="Toggle password visibility">
                  &#128065;
                </button>
              </div>
            </div>
          </section>
        </div>
      </div>
    </section>
  </main>

</body>


</html>