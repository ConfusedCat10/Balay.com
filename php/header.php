<nav>
    <div class="balay-navbar">
        <div class="logo">
            <a href="/bookingapp/index.php"><img src="/bookingapp/assets/site-logo/logo-text-white.png" alt="Balay.com logo">
            </a>
        </div>
        <div class="balay-nav-menu-btn" id="menu-btn">
            <i class="fa-solid fa-bars"></i>
        </div>
    </div>

    <?php
    if ($loggedIn) {
        if ($accountRole === "admin") {
    ?>
            <ul class="balay-nav-links" id="nav-links">
                <li><a href="/bookingapp/establishment/index.php"><i class="fa-solid fa-building"></i> Establishments</a></li>
                <li><a href="/bookingapp/map.php"><i class="fa-solid fa-map"></i> View Map</a></li>
                <li><a href="/bookingapp/admin/accounts.php"><i class="fa-solid fa-key"></i> Accounts</a></li>
                <!-- <li><a href="#home"><i class="fa-solid fa-gear"></i> Settings</a></li> -->
                <li><a href="/bookingapp/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a></li>
                <li><a href="/bookingapp/user/profile.php?id=<?php echo $username; ?>" style="display: flex; wrap: nowrap; align-items: center;"><img src="<?php echo $profilePicture; ?>" style="width: 16px; height: 16px; object-fit: cover; border-radius: 50%; align-items: center; margin-right: 5px"> <?php echo $firstName; ?></a></li>
            </ul>
    <?php 
        } else if ($accountRole === "tenant") {
    ?>
            <ul class="balay-nav-links" id="nav-links">
                <li><a href="/bookingapp/search_result.php"><i class="fa-solid fa-building"></i> Establishments</a></li>
                <li><a href="/bookingapp/tenant/residencies.php"><i class="fa-solid fa-home"></i> Residencies</a></li>
                <li><a href="/bookingapp/map.php"><i class="fa-solid fa-map"></i> View Map</a></li>
                <!-- <li><a href="/bookingapp/tenant/payment_history.php">Payment History</a></li> -->
                <!-- <li><a href="#home">Settings</a></li> -->
                <li><a href="/bookingapp/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a></li>
                <li><a href="/bookingapp/user/profile.php?id=<?php echo $username; ?>" style="display: flex; wrap: nowrap; align-items: center;"><img src="<?php echo $profilePicture; ?>" style="width: 16px; height: 16px; object-fit: cover; border-radius: 50%; align-items: center; margin-right: 5px"> <?php echo $firstName; ?></a></li>
            </ul>
    <?php
        } else if ($accountRole === "owner") { 
    ?>
            <ul class="balay-nav-links" id="nav-links">
                <li><a href="/bookingapp/establishment/index.php"><i class="fa-solid fa-building"></i> Establishments</a></li>
                <li><a href="/bookingapp/establishment/residents"><i class="fa-solid fa-users"></i> My Tenants</a></li>
                <li><a href="/bookingapp/map.php"><i class="fa-solid fa-map"></i> View Map</a></li>
                <!-- <li><a href="#home">Settings</a></li> -->
                <li><a href="/bookingapp/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a></li>
                <li><a href="/bookingapp/user/profile.php?id=<?php echo $username; ?>" style="display: flex; wrap: nowrap; align-items: center;"><img src="<?php echo $profilePicture; ?>" style="width: 16px; height: 16px; object-fit: cover; border-radius: 50%; align-items: center; margin-right: 5px"> <?php echo $firstName; ?></a></li>
            </ul>
    <?php    
        }
    } else { ?>
            <ul class="balay-nav-links" id="nav-links">

                <li><a href="/bookingapp/create_account/create.php?role=tenant"><i class="fa-solid fa-user-plus"></i> Create an Account</a></li>
                <li><a href="/bookingapp/login.php"><i class="fa-solid fa-right-to-bracket"></i> Sign In</a></li>
            </ul>
    <?php } ?>
    <!-- <button class="btn nav-btn" onclick="redirect('/bookingapp/login.php');"><i class="fa-solid fa-right-to-bracket"></i> Sign In</button> -->
</nav>