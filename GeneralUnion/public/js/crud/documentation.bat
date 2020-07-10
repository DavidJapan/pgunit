set documentation_path="C:\Users\David Mann\AppData\Roaming\npm\documentation" 
set project_path=C:/GUDB/gudb0601/GeneralUnion/public/js/ready_for_docs
set config_path=C:\GUDB\gudb0601-jsdoc\config\conf.json
set out_path=C:\GUDB\gudb0601-jsdoc\out
REM %documentation_path% %project_path% -c %config_path% -d %out_path%
REM %documentation_path% --help
%documentation_path% build %project_path%/** -f html -o %out_path%
