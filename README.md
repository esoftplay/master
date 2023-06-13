You can click the header to watch the demo

# [Preparation](https://youtu.be/XAsPVBlLBnI?t=17)
 * Install docker https://www.docker.com/get-started/
 * Make sure these port are available 80, 81 and 3307
 * install git [optional]
 * install framework [optional]
 * install mysql cli [optional]

# [How to install framework [optional]](https://youtu.be/XAsPVBlLBnI?t=316)
 * run command below
 ```bash
 mkdir -p /var/www/html/master
 cd /var/www/html/master
 git clone https://github.com/esoftplay/master.git ./
 docker-compose up -d
 ```
 * create ./config.php as in http://dev.esoftplay.com
 * create database and change salt
 ```bash
 mysql -u root --password='root' --port 3307 -h 127.0.0.1 -e 'DROP DATABASE IF EXISTS master'
 mysql -u root --password='root' --port 3307 -h 127.0.0.1 -e 'CREATE DATABASE IF NOT EXISTS master'
 mysql -u root --password='root' --port 3307 -h 127.0.0.1 master < database.sql
 curl -s -X POST -F 'code='$(date|md5) http://localhost:81/tools/repair/change_salt > /dev/null
 ```
 * open url http://localhost:81/
 * to close it you need to run `docker-compose down`

# [How to run framework](https://youtu.be/XAsPVBlLBnI?t=695)
 * run command below
```bash
cd /var/www/html/master
docker-compose up -d
```

# [How to create new project](https://youtu.be/XAsPVBlLBnI?t=861)
 * run master or edit `docker-compose.yaml` to unmark sql service
 * create new folder and `cd` into this folder
 * run command below
 ```bash
 docker run -it -v $(pwd):/home/sites esoftplay/start
 mysql -u root --password='root' --port 3307 -h 127.0.0.1 -e 'DROP DATABASE IF EXISTS new_project'
 mysql -u root --password='root' --port 3307 -h 127.0.0.1 -e 'CREATE DATABASE IF NOT EXISTS new_project'
 mysql -u root --password='root' --port 3307 -h 127.0.0.1 new_project < database.sql
 docker-compose up -d
 curl -s -X POST -F 'code='$(date|md5) http://localhost/tools/repair/change_salt > /dev/null
 ```
 * open url http://localhost/

# [How to run project](https://youtu.be/XAsPVBlLBnI?t=1412)
 * CD into the project folder
 * run `docker-compose up -d`
 * open url http://localhost/
 * edit the script if necessary

# [Learn more about the this framework](https://youtu.be/XAsPVBlLBnI?t=1568)
 * http://dev.esoftplay.com
