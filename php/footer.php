<footer class="footer" id="contact">
    <div class="section-container footer-container">
        <div class="footer-col">
            <div class="logo">
                <a href="/bookingapp/index.php"><img src="/bookingapp/assets/site-logo/logo-text-white.png" alt="Balay.com logo" style="width: 200px">
                </a>
            </div>
            <div class="section-description">
                Your preimeir online accommodation destination designed exclusively for students at Mindanao State University. Step into a world of comfort with our diverse offerings, including cottages and dormitories. Our unique geotagging feature ensures you find a perfect place to stay. At <span>Balay.com</span>, we're committed to enhancing your student living experience making your stay both enjoyable and memorable.
            </div>
            <?php if ($loggedIn && $accountRole === 'tenant') { ?>
                <button class="btn btn-secondary" onclick="redirect('/bookingapp/search_result.php')">Book Now</button>
            <?php } ?>
        </div>

        <div class="footer-col">
            <h4>QUICK LINKS</h4>
            <ul class="footer-links">
                <li><a href="/bookingapp/map.php">Open Map</a></li>
                <?php if ($loggedIn) {

                    if ($accountRole === 'admin') { ?>
                        <li><a href="/bookingapp/create_account/create.php?role=admin">Create an Admin Account</a></li>
                        <li><a href="/bookingapp/create_account/create.php?role=owner">Create an Owner Account</a></li>
                    <?php } ?>
                    
                    <?php if ($accountRole === 'owner') { ?>
                        <li><a href="/bookingapp/establishment">Browse Establishments</a></li>
                        <li><a href="/bookingapp/establishment/add.php">Add an Establishment</a></li>
                        <li><a href="/bookingapp/establishment/residents/">See Residents</a></li>
                    <?php } ?>

                    <li><a href="/bookingapp/user/profile.php">See Profile</a></li>
                    <li><a href="/bookingapp/logout.php">Sign out</a></li>
                <?php } else { ?>
                    <li><a href="/bookingapp/create_account/create.php">Create an Account</a></li>
                    <li><a href="/bookingapp/login.php">Sign in</a></li>
                <?php } ?>
            </ul>
        </div>

        <div class="footer-col">
            <h4>DEVELOPERS</h4>
            <ul class="footer-links">
                <li><a href="#">Mohammad Noor G. Macalandong</a></li>
                <li><a href="#">Mohammad Namar M. Dimalotang</a></li>
            </ul>

            <br><br>

            <h4>ADVISER</h4>
            <ul class="footer-links">
                <li><a href="#">Prof. Lucman S. Abdulrachman</a></li>
            </ul>

            <br><br>

            <h4>CO-ADVISER</h4>
            <ul class="footer-links">
                <li><a href="#">Prof. Suhaina K. Casim</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>CONTACT US</h4>
            <ul class="footer-links">
                <li><a href="mailto:balay@msumain.edu.ph" title="Open email application">balay@msumain.edu.ph</a></li>
            </ul>
            <!-- <div class="footer-socials">
                <a href="#" title="Open Facebook"><img src="/bookingapp/assets/icons/facebook.png" alt="facebook" /></a>
                <a href="#" title="Open Instagram"><img src="/bookingapp/assets/icons/instagram.png" alt="instagram" /></a>
                <a href="#" title="Open YouTube"><img src="/bookingapp/assets/icons/youtube.png" alt="youtube" /></a>
                <a href="#" title="Open Twitter"><img src="/bookingapp/assets/icons/twitter.png" alt="twitter" /></a>
            </div> -->
        </div>
    </div>
    <div class="footer-bar">
        Copyright &copy; <span id="year"></span> College of Information and Computing Sciences &middot; Mindanao State University - Main Campus.
    </div>
</footer>