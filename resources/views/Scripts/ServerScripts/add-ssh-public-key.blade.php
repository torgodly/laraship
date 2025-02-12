echo "# Forge Key 2389947" | tee -a /home/forge/.ssh/authorized_keys
echo "{{$public_key}}" | tr '\n' ' ' | sed '$s/ $/\n/' | tee -a /home/forge/.ssh/authorized_keys
