"""Build fixture JSON from the RESPONSES: / Example responses: blocks of the official docs.
A function section starts with a '-----' separator followed by a 'name -' heading."""
import json, re, sys
def scrape(path):
    lines = open(path, encoding='utf-8', errors='replace').read().split('\n')
    out, func, after_sep = {}, None, False
    for line in lines:
        if line.startswith('-----'):
            after_sep = True; continue
        if after_sep:
            if line.strip() == '':
                continue
            m = re.match(r'^([a-z][a-z0-9_]*) -', line)
            func = m.group(1) if m else None
            if func: out.setdefault(func, [])
            after_sep = False
            continue
        if func and re.match(r'^(SUCCESS|ERROR|NOTICE): ', line):
            text = line.rstrip()
            if text not in out[func]:
                out[func].append(text)
    return {k: v for k, v in out.items() if v}
agent = scrape(sys.argv[1]); non_agent = scrape(sys.argv[2])
json.dump({'_source': {'agent': 'http://vicidial.org/docs/AGENT_API.txt (updated 2025-08-30)', 'non_agent': 'http://vicidial.org/docs/NON-AGENT_API.txt (updated 2026-09-25)', 'generator': 'see scripts/README.md'},
           'agent': agent, 'non_agent': non_agent}, open(sys.argv[3], 'w'), indent=2)
print('agent functions:', len(agent), 'responses:', sum(len(v) for v in agent.values()))
print('non_agent functions:', len(non_agent), 'responses:', sum(len(v) for v in non_agent.values()))
print(sorted(agent)); print(sorted(non_agent))
