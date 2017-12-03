# PSaaS

Port Scanning As A service

## Program Flow

Program Flow:

1. Frontend takes input
2. Inputs IP and submitted time into scan table, and to rabbitMQ
3. A backend picks up this job, and updates the started time
4. Scans the host
5. Inserts results into scan table, and updates finished time
6. Confirms to rabbitMQ that work was done
7. Frontend keeps reloading every 30s until finished is set, then shows the results when they're done.

## Backend:

### Scan:

    nmap -T1 -P0 -sTUV --top-ports=100 -A <REMOTE_ADDR>


## Frontend:

### Form:

    Ports:

    ( ) --top-ports=50
    ( ) --top-ports=100
    ( ) -p (custom) (ex: T:21,22,23,80,443,110,143,993,995,U:123,161,500,4500)  ________

    Protocols:

    [ ] TCP
    [ ] UDP

    Speed:

    Slower speed scans can sometimes avoid being blocked by active firewalls.

    ( ) SLOW (1h)
    ( ) Medium (2-3m)
    ( ) FAST (<1m)

    Service Info:

    [ ] -A

    Legal/Acknowledgement:

    [ ] YES I AM AUTHORIZED TO BE RUNNING THIS PORT SCAN.

## Installation:

This program uses `composer.phar`.

`php composer.phar install`
