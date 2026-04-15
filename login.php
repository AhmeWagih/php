<?php
require('connection.php');
require('./classes/User.php');
session_start();

$statusMessage = '';
$emailValue = '';

if (isset($_POST['email']) && isset($_POST['password'])) {
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $emailValue = $email;

  $userModel = new User($database);
  $user = $userModel->findByEmail($email);

  if ($user !== null) {
    if ($user['password'] === $password) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['namename'] = $user['first_name'] . ' ' . $user['last_name'];
      header("Location: details.php?id=" . urlencode((string) $user['id']));
      exit();
    } else {
      $statusMessage = 'Invalid password.';
    }
  } else {
    $statusMessage = 'User not found.';
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
  <main class="min-h-screen px-4 py-10 sm:px-6 flex items-center justify-center">
      <div class="order-1 lg:order-2">
        <div
          class="rounded-3xl border border-orange-200 bg-white/90 p-6 shadow-[0_20px_70px_rgba(194,65,12,0.2)] sm:p-8">
          <h2 class="title-font text-2xl text-orange-900">Login</h2>
          <p class="mt-1 text-sm text-orange-700">Enter your credentials to continue.</p>

          <?php if ($statusMessage !== ''): ?>
            <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
              <?php echo htmlspecialchars($statusMessage, ENT_QUOTES, 'UTF-8'); ?>
            </div>
          <?php endif; ?>


          <form action="login.php" method="POST" class="mt-5 space-y-4">
            <div>
              <label for="email" class="mb-1 block text-sm font-semibold text-orange-900">Email</label>
              <input id="email" type="email" name="email" required
                value="<?php echo htmlspecialchars($emailValue, ENT_QUOTES, 'UTF-8'); ?>"
                class="w-full rounded-xl border border-orange-200 bg-white px-3 py-2.5 text-sm text-orange-900 outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200"
                placeholder="email@example.com">
            </div>

            <div>
              <label for="password" class="mb-1 block text-sm font-semibold text-orange-900">Password</label>
              <input id="password" type="password" name="password" required
                class="w-full rounded-xl border border-orange-200 bg-white px-3 py-2.5 text-sm text-orange-900 outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200"
                placeholder="Enter your password">
            </div>

            <button type="submit"
              class="w-full rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-300 focus:ring-offset-2">
              Sign In
            </button>
          </form>
        </div>
      </div>
  </main>
</body>
</html>