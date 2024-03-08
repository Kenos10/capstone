<link rel="stylesheet" href="css/usersModal.css?version=1">
<div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>User Registration</h3>
            <form action="" method="POST">
                <div>
                    <legend>Student Details</legend>
                    <div class="form-group">
                        <div class="form-search">
                            <span><img src="../icons/search-interface-symbol.png" alt="search"></span>
                            <label for="search">Search Officer</label>
                            <input type="search" name="search" id="searchuser" placeholder="Search..." autocomplete="off">
                            <div id="resultuser"></div>
                        </div>

                        <div>
                            <label for="username">Username</label>
                            <input type="text" name="username" required>
                        </div>

                        <div>
                            <label for="name">Name</label>
                            <input type="text" name="name" id="fullname-input" readonly>
                        </div>

                        <div>
                            <label for="password">Password</label>
                            <input type="password" name="password" required>
                        </div>

                        <div>
                            <label for="position">Position</label>
                            <input type="text" name="position" id="position-input" readonly>
                        </div>
                        
                        <div>
                            <label for="confpass">Confirm Password</label>
                            <input type="password" name="confpass" required>
                        </div>

                        <div>
                            <label for="role">Role</label>
                            <select name="role" id="" required>
                                <option>--Role--</option>
                                <option value="Administrator">Administrator</option>
                                <option value="Attendance Manager">Attendance Manager</option>
                                <option value="Event Manager">Event Manager</option>
                            </select>
                        </div>

                        <div>
                            <input type="number" name="sboid" id="sboid-input" readonly hidden>
                        </div>

                    </div>
                </div>
                <input type="submit" name="add" value="Submit">
        </form>
        </div>
    </div>