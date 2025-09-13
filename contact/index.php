<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact Us</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <?php include("../header/index.php"); ?>

  <div class="container">
    <h1 style ="margin-top: 150px;">Contact Us</h1>

    <form action="#" method="post">
      <label for="name">Name</label>
      <input type="text" id="name" name="name" placeholder="Your full name" required />

      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="Your email address" required />

      <label for="message">Message</label>
      <textarea id="message" name="message" rows="6" placeholder="Write your message here..." required></textarea>

      <button type="submit">Send Message</button>
    </form>

    <div style="margin-top: 40px;">
  <h1 style="text-align: center; color: #ff6600;">Our Location</h1>
  <div class = "addressContainer" >
    <div class ="word">
      <h2>Neo Chean Ming</h2>
      <p>Telephone: +60 12 223 1234</p>
      <p>Email: xx@gmail.com</p>
      <p> Location: Jalan Sungai Long, Bandar Sungai Long, 43000 Kajang, Selangor</p>
    </div>
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127492.08319207394!2d101.72871372297307!3d3.060527843490911!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc34a5f21a8235%3A0x78796ffc32ce3fcd!2sUniversiti%20Tunku%20Abdul%20Rahman%20(UTAR)!5e0!3m2!1sen!2smy!4v1757494244135!5m2!1sen!2smy" width="100%" height="450px" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
  </div>
</div>
  </div>
  <?php include("../footer/index.php"); ?>
</body>
</html>