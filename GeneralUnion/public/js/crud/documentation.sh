#!/bin/bash
DOCUMENTATION_PATH="C:/Users/David Mann/AppData/Roaming/npm/documentation" 
PROJECT_PATH="C:/GUDB/gudb0601/GeneralUnion/public/js/ready_for_docs"
CONFIG_PATH="C:/GUDB/gudb0601/GeneralUnion/public/js/crud/documentation.yml"
OUT_PATH="C:/GUDB/gudb0601-jsdoc/out/documentationjs"
"$DOCUMENTATION_PATH" build $PROJECT_PATH/** -f html -o $OUT_PATH --config $CONFIG_PATH
