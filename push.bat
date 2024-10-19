RENAME .git .main_git
start git init
start git remote add origin https://github.com/Noctissinnit/booking.git
start git add .
start git commit -m "Initial Commit"
start git push origin main
@RD /S /Q .git
RENAME .main_git .git