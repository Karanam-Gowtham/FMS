from pathlib import Path 
lines=Path('modules/auth/reg.php').read_text().splitlines() 
start=max(0,201) 
for i in range(start,min(len(lines),start+30)): 
    print(f\"{i+1}: {lines[i]}\") 
