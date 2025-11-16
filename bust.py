#!/usr/bin/env python3
from sys import argv
import urllib.request
from sys import argv
argc = len(argv)
status = {
    200 : "Exists",
    301 : "Directory",
    302 : "Redirection",
    403 : "Forbidden but exists",
    404 : "Not found (ignore)",
    500 : "Rare but can be interesting",
}
def fetch(url,wordlist):
    try:
        req = urllib.request.Request(url, method='GET')
        with urllib.request.urlopen(req, timeout=3) as res:
            if res.status in status:
                print(f"{url} => {res.status} ( {status[res.status]} )")
                if res.status in (200, 301):
                    bruteforce(url,wordlist)
            return res.status
    except Exception:
        return None
def bruteforce(base, wordlist):
    for word in open(wordlist, 'r'):
        word = word.strip()
        if not word:
            continue
        url = f"{base}/{word}"
        status = fetch(url,wordlist)
host        = None
wordlist    = None
if argc > 1:
    host = argv[1]
    if argc > 2:
        wordlist = argv[2]
host        =   host    if host     else    input("input host:\n> ")
wordlist    =   wordlist  if wordlist   else    input("input content file:\n> ")
print("\nresults:\n")
try:
    bruteforce(host,wordlist)
except KeyboardInterrupt as e:
    print("closing calmly...")
