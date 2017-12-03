<!DOCTYPE html>
<html lang="en">
  <head>
    <title>PSaaS</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


  </head>

  <body>

    <ul class="breadcrumb">
      <li class='active'><a href="/">Port Scan as a Service</a></li>
    </ul>

    <div class="container">

      <h1>Port Scan as a Service</h1>

	  <p>This will run a nmap scan against your IP, <?php echo $_SERVER['REMOTE_ADDR']; ?>.</p><p>The following form will configure how that scan is run.</p>

      <form method=POST action=request.php>
        <div class="form-group">
          <label for="ports">Ports to scan</label>
          <div class="radio">
            <label>
              <input type="radio" name="ports" id="ports" value="top50">
              --top-ports=50
            </label>
          </div>
          <div class="radio">
            <label>
              <input type="radio" name="ports" id="ports" value="top100">
              --top-ports=100
            </label>
          </div>
          <div class="radio">
            <label>
              <input type="radio" name="ports" id="ports" value="custom">
              -p (custom)
            </label>
            <input type="text" class="form-control" name="customports" id="customports" placeholder="Custom Ports">
            <p class="help-block">ex: T:21,22,23,80,443,110,143,993,995,U:123,161,500,4500</p>
          </div>
        </div>

        <div class="form-group">
          <label for="protocols">Protocols:</label>
          <div class="checkbox">
            <label>
              <input type="checkbox" name="tcp" checked> TCP
            </label>
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox" name="udp"> UDP
            </label>
          </div>
        </div>

        <div class="form-group">
          <label for="speed">Speed:</label>
          <div class="radio">
            <label>
              <input type="radio" name="speed" id="speed" value="slow">
              SLOW (1h)
            </label>
            <p class="help-block">Slower speed scans can sometimes avoid being blocked by active firewalls.</p>
          </div>
          <div class="radio">
            <label>
              <input type="radio" name="speed" id="speed" value="medium">
              Medium (2-3m)
            </label>
          </div>
          <div class="radio">
            <label>
              <input type="radio" name="speed" id="speed" value="fast">
              FAST (&lt;1m)
            </label>
            <p class="help-block alert-danger">WARNING WILL ROBINSON. FAST scans will get you blocked very quickly by active firewalls.</p>
          </div>
        </div>

        <div class="form-group">
          <label for="extrainfo">Service Info:</label>
          <div class="checkbox">
            <label>
              <input type="checkbox" name="extrainfo"> -A
            </label>
          </div>
        </div>

        <div class="form-group">
          <label for="authorized">Legal/Acknowledgement:</label>
          <div class="checkbox">
            <label>
              <input type="checkbox" name="authorized"> YES I AM AUTHORIZED TO BE RUNNING THIS PORT SCAN.
            </label>
            <p class="help-block alert-danger">WARNING: running port scans against systems which do you not operate can get you into legal trouble. This acknowledges that you have authorization and accept any risks involved.</p>
          </div>
        </div>

        <button type="submit" class="btn btn-default">Submit to Queue</button>
      </form>
    </div>


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

  </body>
</html>
