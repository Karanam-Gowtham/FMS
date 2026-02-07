import re, pathlib 
text=pathlib.Path('database/project-fms').read_text(encoding='utf-8',errors='ignore') 
m=re.search(r'CREATE TABLE `reg_dept_cord`\s*\((.*?)\) ENGINE', text, re.S) 
print(bool(m)) 
if m: 
    print(m.group(1)) 
