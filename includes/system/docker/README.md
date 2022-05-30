# How to use
cd in to this directory and run
docker build -t master:0 .
img=??
docker create $img
docker ps -a
con=??
docker run -p 80:80 $img -d
docker exec -it $con /bin/bash
docker stop $con && docker rm $con && docker rmi $img

# How to push
docker ps
imageID=??
docker container commit $imageID master:latest
docker image tag master:latest esoftplay/master
docker push esoftplay/master:latest
