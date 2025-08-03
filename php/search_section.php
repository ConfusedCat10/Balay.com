<!-- Search section -->
<section class="section-container booking-container booking-form">
    <!-- <form method="get" action="/bookingapp/search_result.php" class="booking-form"> -->
        <input type="hidden" name="filter" value="true">
        <div class="input-group" style="width: 100%; justify-content: center">
            <span><i class="fa-solid fa-building"></i></span>
            <div class="dropdown" style="width: 50%;">
                <label for="">Search dormitories and cottages</label>
                <input type="text" name="search" id="searchInput" placeholder="Search establishment" oninput="searchEstablishments()" style="width: 80%; max-width: 500px">
            </div>
            <!-- <button class="btn btn-primary" style="background-color: maroon; color: white" onclick="redirect('/bookingapp/search_result.php')"><i class="fa-solid fa-magnifying-glass"></i> SEARCH</button> -->

        </div>
    <!-- </form> -->
   </section>
   <!-- End of search section -->
   
   <script>
        // Get today's date
        const today = new Date();
        
        // Get next month
        const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, 1);

        // Format to YYYY-MM for input
        const minMonth = nextMonth.toISOString().slice(0, 7);

        // Set the min attribute of the month input to the next month
        // document.getElementById("inclusiveMonths").setAttribute("min", minMonth);

        function changeValue(change, inputID) {
            const input = document.getElementById(inputID);
            let currentValue = parseInt(input.value);
            let newValue = currentValue + change;

            if (newValue > input.min && newValue <= input.max) {
                input.value = newValue;
            }
        }
   </script>