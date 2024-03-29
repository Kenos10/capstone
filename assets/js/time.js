(function() {
    'use strict';

    function getDate() {
        var date = new Date();
        var weekday = date.getDay();
        var month = date.getMonth();
        var day = date.getDate();
        var year = date.getFullYear();
        var hour = date.getHours();
        var minutes = date.getMinutes();
        var seconds = date.getSeconds();

        if (hour < 10) hour = "0" + hour;
        if (minutes < 10) minutes = "0" + minutes;
        if (seconds < 10) seconds = "0" + seconds;

        var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        var weekdayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        var ampm = " pm ";

        if (hour < 12) ampm = " am ";
        if (hour > 12) hour -= 12;

        var showDate = weekdayNames[weekday] + ", " + monthNames[month] + " " + day + ", " + year;
        var showTime = hour + ":" + minutes + ":" + seconds + ampm;

        document.getElementById('date').innerHTML = showDate;
        document.getElementById('time').innerHTML = showTime;
        
        requestAnimationFrame(getDate);
    }

    getDate();

}());