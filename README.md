# aurora-forecast
My very own aurora forecast which runs as a cron every hour on my server.
I crawl an image of the northern hemisphere from services.swpc.noaa.gov and analyse a pixel which is located approximately on my geo location. 
If the analyse was successfull and it's likely to see an aurora at my location i do a get request to my IFTTT maker channel to receive a push message on my smartphone. 

# todo
* Add sunrise/sunset
* Add moon phases
* Add KP index
* Add ocr for fun (thiagoalessio/tesseract_ocr) to read the forecast times from the image