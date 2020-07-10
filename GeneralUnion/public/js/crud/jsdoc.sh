#!/bin/bash
JSDOC_PATH="C:/Users/David Mann/AppData/Roaming/npm/jsdoc" 
#I run this shell script in folder containing the files I want to document
#Doing it that way, I can list the files I want to document with just their name.
PROJECT_PATH="namespace.js"
CONFIG_PATH="C:/GUDB/gudb0602-jsdocs/config/conf.json"
OUT_PATH="C:/GUDB/gudb0602-jsdocs/out"
"$JSDOC_PATH" $PROJECT_PATH -c $CONFIG_PATH -d $OUT_PATH
