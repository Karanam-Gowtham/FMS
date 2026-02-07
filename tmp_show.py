from pathlib import Path 
lines=Path('modules/auth/reg.php').read_text().splitlines() 
start=max(0,202-10) 
    print(f\"{i+1}: {lines[i]}\") 
