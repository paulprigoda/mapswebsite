<!DOCTYPE html>
<html>
<head>
     <link rel="stylesheet" type="text/css" href="HW6.css?v={0}">
      </head>

<script>
  
    var mX = localStorage.xlocal;
    var mY = localStorage.ylocal;
    
        function localshiz(x,y){
            if(typeof(Storage)!=="undefined"){
                if( localStorage.getItem("xlocal")){
                    if(mX == null){
                        mX = localStorage.xlocal;
                        mY = localStorage.ylocal;
                    }

                    else{
                        localStorage.xlocal = mX;
                        localStorage.ylocal = mY;
                    }
                }
                else{
                        localStorage.xlocal = mX;
                        localStorage.ylocal = mY;
                    }
                }
            }
    
        function draw() {
            var canv=document.getElementById("map");
            var c=canv.getContext("2d");
            var img = new Image();
            var w, h;

            img.onload = function(){
                w=canv.width;		// resize the canvas to the new image size
                h=canv.height;
                c.drawImage(img, 0, 0, w, h );

                c.strokeStyle = "black";
                c.fillStyle = "rgba(255,255,255,0.5)";
                c.lineWidth = 1;
                c.beginPath();
                c.arc(mX, mY, 10, 0, Math.PI*2);
                c.closePath();
                c.stroke();
                c.fill();

                c.strokeStyle = "black";
                c.fillStyle = "rgba(255,255,255,0.75)";
                c.lineWidth = 1;
                c.beginPath();
                c.arc(mX, mY, 2, 0, Math.PI*2);
                c.closePath();
                c.stroke();
                c.fill();
                console.log(mX,mY)
            }
            img.src = 'staticmap.png';
        }

        function getMousePos(canvas, events){
            draw();
            var obj = canvas;
            var top = 0, left = 0;

                while (obj && obj.tagName != 'BODY') { //accumulate offsets up to 'BODY'
                top += obj.offsetTop;
                left += obj.offsetLeft;
                obj = obj.offsetParent;
                
            }
            mX = events.clientX - left + window.pageXOffset;
            mY = events.clientY - top + window.pageYOffset;
            
            var Xcoord = -(122.4194 - ((mX-47)*(.0561)));
            var Ycoord = (47.4875 - ((mY-27)*(.0432)));
            var xF = Math.round(Xcoord*100000)/100000;
            var yF = Math.round(Ycoord*10000)/10000;
            
            localshiz(mX,mY);
            
            return { x: xF, y: yF, mX, mY };
        }
            
        window.onload = function(){
            var c = document.getElementById('map');
            c.addEventListener('mousedown', function(events){
                var mousePos = getMousePos(c, events);
                var tx = document.getElementById("xpos");
                tx.value = mousePos.x;
                var ty = document.getElementById("ypos");
                ty.value = mousePos.y;       
            });
          draw();
        }
    
    //getting long and latitude by click point done in here
    //it is a function
    
    //take lat and lon of LA and 
    </script>
    
<body>
    <div id = "back">
       <div id="title">
           <form action="HW6.php" method="get">
               <h1> THE COM214 ZIP CODE LOCATOR </h1>
           <button id="createDB" name="button" value="create" type="submit">Create DB</button>
            <button id="dropDB" name="button" value="delete" type="submit">Drop DB</button>
            </form>
        </div>
    </div>
        
        <div id = "mapdiv">
        <canvas id = "map" width="1000" height="500"></canvas>
        </div>
        
    <div id="bottom">
        <div id = "latlong">
        <form action="HW6.php" method="get">
        LATITUDE:  <input type="text" id="ypos" name="ypos" readonly="readonly">
        LONGITUDE:  <input type="text" id="xpos" name="xpos" readonly="readonly">
        
            <button id = "nearby" value = "getzip" type="submit" name="button">List Nearby Zipcodes</button>
             <h5>Items per page</h5>
             <select id = "pages" value="pages" name="pages" >
             <option value = "5">5</option>
             <option value = "10">10</option>
             <option value = "20">20</option>
             <option value = "30">30</option>
             <option value = "50">50</option>
                 
             </select>
        </form>
        </div>

    <div id="phph">
    <?php 
        
        function latLonToMiles($lat1, $lon1, $lat2, $lon2){  //Haversine formula
            $R = 3961;  // radius of the Earth in miles
            $dlon = ($lon2 - $lon1)*M_PI/180;
            $dlat = ($lat2 - $lat1)*M_PI/180;
            $lat1 *= M_PI/180;
            $lat2 *= M_PI/180;
            $a = pow(sin($dlat/2),2) + cos($lat1) * cos($lat2) * pow(sin($dlon/2),2) ;
            $c = 2 * atan2( sqrt($a), sqrt(1-$a) ) ;
            $d = $R * $c;
            return $d;
        }
        
        $db_zip = mysqli_connect("localhost", "root", "");
        
        if (!$db_zip)
            die("Unable to connect: " . mysqli_connect_error());  // die is similar to exit
        
        if (isset($_GET["button"])){
            if( $_GET["button"] == "create"){
                if (mysqli_query($db_zip, "CREATE DATABASE  zipcodes;")){
                    echo "database creates";
                mysqli_select_db($db_zip, "zipcodes"); 
                $cmd = "CREATE TABLE locator ( 
                zipcode varchar(5) NOT NULL PRIMARY KEY,
                        city varchar(25),
                        state varchar(2),
                        lat float(7,4),
                        lon float(7,4),
                        timeDiff int(1)
                        );";
                    
				    echo "Database ready<br>";
                }
                
			     else{
				    echo "Unable to create database: " . mysqli_error($db_zip) . "<br>";
                 }
            }
                
            if ( $_GET["button"] == "delete"){
                 $retval = mysqli_query($db_zip , "DROP DATABASE zipcodes;");
			     if(!$retval )
                     die('Unable to delete database: ' . mysqli_error($db_zip));
                
                echo "Database deleted successfully\n";
                
            }
        }
        
        if (isset($_GET["button"])){
            if( $_GET["button"] == "getzip"){
                     
                mysqli_select_db($db_zip, "zipcodes");

                $cmd = "CREATE TABLE locator ( 
                    zipcode varchar(5) NOT NULL PRIMARY KEY,
                            city varchar(25),
                            state varchar(2),
                            lat float(7,4),
                            lon float(7,4),
                            timeDiff int(1)
                            );";

                mysqli_query($db_zip, $cmd);

                $cmd = "LOAD DATA LOCAL INFILE 'zip_codes_usa.csv' INTO TABLE locator FIELDS TERMINATED BY ',' ;"; 
                    
                mysqli_query($db_zip, $cmd);

                $page = $_GET["pages"];
                $xpos = $_GET["xpos"];
                $ypos = $_GET["ypos"];

                $cmd = "SELECT *,
                SQRT(POW(lon-(".$xpos."),2) + POW(lat-(".$ypos."),2)) AS len1 FROM locator ORDER BY len1 ASC LIMIT ".$page.";";

                //echo($cmd);
                $records = mysqli_query($db_zip, $cmd);

                echo( "<table border = 'black' align:'center'> 
                    <tr> 
                    <th>Zip Code</th> 
                    <th>City</th> 
                    <th>State</th> 
                    <th>Lat</th> 
                    <th>Lon</th> 
                    <th>Miles</th>
                    <th>Time Diff (ET)</th> 
                    </tr>" . PHP_EOL  );


                while($row = mysqli_fetch_array($records)){
                    $dist = latLonToMiles($row['lon'], $row['lat'], $xpos, $ypos);
                    echo( "<tr> 
                            <td id = 'yellow'>" . $row['zipcode'] . "</td> 
                            <td id = 'red'>" . $row['city'] . "</td> 
                            <td id = 'red'>" . $row['state'] . "</td> 
                            <td id = 'blue'>" . $row['lat'] . "</td>
                            <td id = 'blue'>". $row['lon'] . "</td>
                            <td id = 'pink'>". number_format($dist, 2, '.','') . "</td>
                            <td id = 'pink'>". ($row['timeDiff']+5) . "</td>
                            </tr>". PHP_EOL );
                }
            echo("</table>");
            }
        }
            mysqli_close($db_zip); //close connection
		?>  
    </div>
    </div>
    </body>
</html>