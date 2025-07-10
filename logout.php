<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Logging Out...</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" />
</head>
<body>

<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script>
  Toastify({
    text: "👋 You have been logged out successfully!",
    duration: 500,
    gravity: "top",
    position: "center",
    backgroundColor: "#dc3545",
    stopOnFocus: true,
    callback: function () {
      window.location.href = "login.php";
    }
  }).showToast();
</script>

</body>
</html>
