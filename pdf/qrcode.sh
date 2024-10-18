#!/bin/bash
docker container run -v /Docker/$1:/work --rm  qrcode qrencode -o qrcode.png "$(cat qrcode.dat)"
