<?php
$errorText = $_GET["errors"] ?? "";
$errorList = array_filter(array_map('trim', explode(',', $errorText)));

$fieldErrors = [
    'first_name' => [],
    'last_name' => [],
    'email' => [],
    'password' => [],
    'image' => [],
];

foreach ($errorList as $error) {
    $normalized = strtolower($error);

    if (strpos($normalized, 'first name') !== false) {
        $fieldErrors['first_name'] = $error;
    } elseif (strpos($normalized, 'last name') !== false) {
        $fieldErrors['last_name'] = $error;
    } elseif (strpos($normalized, 'email') !== false) {
        $fieldErrors['email'][] = $error;
    } elseif (strpos($normalized, 'password') !== false) {
        $fieldErrors['password'][] = $error;
    } elseif (strpos($normalized, 'image') !== false) {
        $fieldErrors['image'][] = $error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50">
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-semibold leading-tight text-slate-800 mb-3">Add User</h1>
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <form action="users.php" method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">First Name</label>
                        <input type="text" name="first_name" placeholder="John"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none ring-0 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                            required>
                        <?php foreach ($fieldErrors['first_name'] as $message): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo ($message); ?></p>
                        <?php endforeach; ?>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Last Name</label>
                        <input type="text" name="last_name" placeholder="Doe"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none ring-0 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                            required>
                        <?php foreach ($fieldErrors['last_name'] as $message): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo ($message); ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Department</label>
                        <input type="text" name="department" placeholder="Open Source"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none ring-0 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Country</label>
                        <select name="country"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-500 outline-none ring-0 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                            <option selected>Select a country</option>
                            <option value="Egypt">Egypt</option>
                            <option value="USA">USA</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Residential Address</label>
                    <textarea name="address"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none ring-0 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                        rows="3" placeholder="Enter full street address, city, and postal code..."></textarea>
                </div>

                <div class="mt-4">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Gender</label>
                    <div class="flex flex-wrap gap-6">
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                            <input class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500" type="radio"
                                name="gender" id="male" value="male">
                            Male
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                            <input class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500" type="radio"
                                name="gender" id="female" value="female">
                            Female
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                            <input class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500" type="radio"
                                name="gender" id="other" value="other">
                            Other
                        </label>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                        <input type="email" name="email"
                            class="w-full rounded-lg border border-blue-100 bg-blue-50 px-3 py-2 text-sm outline-none ring-0 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                            placeholder="email@example.com">
                        <?php foreach ($fieldErrors['email'] as $message): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo ($message); ?></p>
                        <?php endforeach; ?>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                        <div class="flex overflow-hidden rounded-lg border border-blue-100 bg-blue-50">
                            <input id="pass" type="password" name="password"
                                class="w-full bg-transparent px-3 py-2 text-sm outline-none" value="">
                            <button
                                class="inline-flex items-center border-l border-blue-100 px-3 text-slate-500">Show</button>
                        </div>
                        <?php foreach ($fieldErrors['password'] as $message): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo ($message); ?></p>
                        <?php endforeach; ?>
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
                                    <input class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                        type="checkbox" name="skills[]" value="<?php echo $skill; ?>">
                                    <?php echo $skill; ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Verification</label>
                    <div class="flex flex-wrap items-center gap-3 rounded-lg border border-slate-200 p-3">
                        <div
                            class="rounded-md border border-slate-200 bg-white px-4 py-2 font-mono text-sm font-bold italic tracking-[0.35em] text-slate-700">
                            X 7 2 B P</div>
                        <button type="button"
                            class="text-sm font-medium text-indigo-600 transition hover:text-indigo-700">Refresh</button>
                        <input type="text" name="captcha"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none ring-0 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 sm:max-w-[200px]"
                            placeholder="Enter captcha">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Profile Image</label>
                    <div>
                        <input type="file" name="image"
                            class="w-full cursor-pointer rounded-lg border border-slate-300 bg-white text-sm file:mr-4 file:cursor-pointer file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200"
                            id="inputGroupFile02">
                        <?php foreach ($fieldErrors['image'] as $message): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo ($message); ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>

                <hr class="my-6 border-slate-200">

                <div class="mt-4 flex justify-end gap-3">
                    <button type="button"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-500 transition hover:bg-slate-100">Cancel</button>
                    <button type="submit"
                        class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">Add
                        User</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>