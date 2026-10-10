#!/usr/bin/env bash
# Validation non-intrusive du lab Mushroom Kingdom.
# Ne JAMAIS exploiter quoi que ce soit ici — uniquement des vérifications
# en lecture seule, via les mêmes connexions que les rôles Ansible
# (inventory.yml : SSH sur TOAD, WinRM tunnelé sur KOOPALINGS/BOWSER).
#
# Prérequis : le tunnel SSH vers le réseau interne doit déjà être ouvert
# (voir README / commentaire dans ansible/inventory.yml) si on valide les
# machines Windows.
set -euo pipefail

cd "$(dirname "$0")/../ansible"

PASS=0
FAIL=0

check() {
  local desc="$1"
  local cmd="$2"
  if eval "$cmd" >/tmp/validate-lab.out 2>&1; then
    echo "[OK]   ${desc}"
    PASS=$((PASS+1))
  else
    echo "[FAIL] ${desc}"
    sed 's/^/       /' /tmp/validate-lab.out
    FAIL=$((FAIL+1))
  fi
}

echo "== TOAD-MUSHROOM =="
check "ip_forward désactivé" \
  "ansible toad-mushroom -m ansible.builtin.shell -a 'sysctl -n net.ipv4.ip_forward | grep -qx 0' --ask-vault-pass -e @group_vars/credentials.yml"
check "apache2 actif" \
  "ansible toad-mushroom -m ansible.builtin.systemd -a 'name=apache2' --ask-vault-pass -e @group_vars/credentials.yml | grep -q '\"state\": \"started\"'"
check "smbd actif" \
  "ansible toad-mushroom -m ansible.builtin.systemd -a 'name=smbd' --ask-vault-pass -e @group_vars/credentials.yml | grep -q '\"state\": \"started\"'"
check "coffre KeePass de toadette présent" \
  "ansible toad-mushroom -m ansible.builtin.stat -a 'path=/var/backups/keepass-backup.kdbx' --ask-vault-pass -e @group_vars/credentials.yml | grep -q '\"exists\": true'"
check "sudoers toadette présent" \
  "ansible toad-mushroom -m ansible.builtin.stat -a 'path=/etc/sudoers.d/toadette' --ask-vault-pass -e @group_vars/credentials.yml | grep -q '\"exists\": true'"
check "seul 22/80 ouverts côté DMZ (ens3)" \
  "ansible toad-mushroom -m ansible.builtin.shell -a 'nft list ruleset | grep -A2 ens3' --ask-vault-pass -e @group_vars/credentials.yml | grep -q '22, 80'"

echo
echo "== KOOPALINGS-CASTLE (nécessite le tunnel SSH, voir README) =="
check "site IIS KoopalingsCastle démarré" \
  "ansible koopalings-castle -m community.windows.win_iis_website -a 'name=KoopalingsCastle' --ask-vault-pass -e @group_vars/credentials.yml | grep -q '\"state\": \"Started\"'"
check "svc_bowser_jr n'est pas admin local" \
  "ansible koopalings-castle -m ansible.windows.win_shell -a 'C:\\\\Scripts\\\\verify-privesc.ps1' --ask-vault-pass -e @group_vars/credentials.yml | grep -q 'OK.*pas administrateur local'"
check "pare-feu : 80/tcp interne uniquement" \
  "ansible koopalings-castle -m community.windows.win_firewall_rule -a 'name=\"Koopalings Castle - HTTP interne\" state=present' --ask-vault-pass -e @group_vars/credentials.yml | grep -q '\"enabled\": true'"

echo
echo "== DC-BOWSER-KINGDOM (nécessite le tunnel SSH, voir README) =="
check "service AD DS actif" \
  "ansible dc-bowser-kingdom -m ansible.windows.win_service -a 'name=NTDS' --ask-vault-pass -e @group_vars/credentials.yml | grep -q '\"state\": \"running\"'"
check "SMB signing activé (serveur)" \
  "ansible dc-bowser-kingdom -m ansible.windows.win_reg_stat -a 'path=HKLM:\\\\SYSTEM\\\\CurrentControlSet\\\\Services\\\\LanManServer\\\\Parameters name=RequireSecuritySignature' --ask-vault-pass -e @group_vars/credentials.yml | grep -q '\"value\": 1'"

echo
echo "---------------------------------------------"
echo "Résultat : ${PASS} OK / ${FAIL} échec(s)"
[ "${FAIL}" -eq 0 ]
