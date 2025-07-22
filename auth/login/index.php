<?php
include_once("../../config/config.php");
// Input values
$email = '';
$password = '';

// Input error
$emailErr = '';
$passwordErr = '';

// Login error
$loginErr = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST['email']) ?? '';
  $password = trim($_POST['password']) ?? '';

  // Initialize all the error to empty string
  $emailErr = $passwordErr = "";

  $hasErr = false;

  if (empty($email)) {
    $emailErr = "Email is required";
    $hasErr = true;
  } else if (!validateEmail($email)) {
    $emailErr = "Please enter a valid email";
    $hasErr = true;
  }

  if (empty($password)) {
    $passwordErr = "Password is required";
    $hasErr = true;
  } else if (strlen($password) < 6) {
    $passwordErr = "Password must have at least 6 chacters";
    $hasErr = true;
  } else if (!validatePassword($password)) {
    $passwordErr = "Password must contain at least one special character(s)";
    $hasErr = true;
  }

  // Only proceed if email and password input are valid
  if (!$hasErr) {

    $user = "select password from user where email = ?";

    $result = $conn->execute_query($user, [trim($email)]);

    if (!$result) {
      $loginErr = "server error. please try again later: " . $conn->error;
    }

    if ($result->num_rows === 0) {
      $loginErr = "email does not exists";
    }

    $row = $result->fetch_assoc();

    $hashedPassword = $row['password'];

    if (!password_verify($password, $hashedPassword)) {
      $loginErr = "password is incorrect";
    }
  }


  $conn->close();
}

function validateemail(string $email)
{
  $emailregex = "/\w+@\w+\.\w+/";
  return preg_match($emailregex, $email) === 1;
}

function validatepassword(string $password)
{
  // at least one special character
  $passwordregex = "/[^a-za-z0-9]/";
  return preg_match($passwordregex, $password) === 1;
}

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link rel="stylesheet" href="./login-style.css">
  <link rel="stylesheet" href="../../index.css">
  <title>Login</title>
</head>

<body>
  <main class="max-w-[500px] w-full mx-auto mt-20 bg-gradient-to-br from-gray-800 via-slate-700 to-gray-900 border border-gray-600 rounded-xl shadow-2xl text-white font-medium p-6">
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="flex flex-col gap-6" id="loginFormContainer">
      <h2 class="text-3xl font-bold text-center text-cyan-400">Login Now</h2>

      <!-- Email Field -->
      <label class="flex flex-col gap-1">
        <span class="text-sm text-gray-300">Email</span>
        <input type="text" id="email" name="email" required
          value="<?php echo htmlspecialchars($email) ?>"
          class="bg-gray-900 text-white border border-gray-600 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all duration-200" />
        <!-- Email error message -->
        <span class="text-red-400 text-sm h-5 err-msg"><?php echo htmlspecialchars($emailErr) ?></span>
      </label>

      <!-- Password Field -->
      <label class="flex flex-col gap-1">
        <span class="text-sm text-gray-300">Password</span>
        <input type="password" id="password" name="password" required
          value="<?php echo htmlspecialchars($password) ?>"
          class="bg-gray-900 text-white border border-gray-600 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all duration-200" />
        <!-- Password error message -->
        <span class="text-red-400 text-sm h-5 err-msg"><?php echo htmlspecialchars($passwordErr) ?></span>
      </label>

      <!-- Submit Button -->
      <button type="submit"
        class="bg-cyan-500 hover:opacity-70 text-white font-semibold py-2 rounded-md transition-opacity uppercase tracking-wide">
        Login
      </button>
    </form>

    <div class="text-center mt-6 text-sm text-gray-300">
      Don't have an account?
      <a href="../register/" class="text-cyan-400 hover:underline hover:text-cyan-300">Sign Up here!</a>
    </div>
  </main>

</body>
<script>
  // Get all labels
  const inputLabels = document.querySelectorAll("label");

  inputLabels.forEach(label => {
    const inputField = label.querySelector("input")
    const errSpan = label.querySelector("span.err-msg");

    // Clear each input field's error message when user start typing
    inputField.addEventListener("input", () => errSpan.innerHTML = "");

  })
</script>

</html>
