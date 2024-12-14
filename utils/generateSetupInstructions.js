function generateAuthInstructions(authMethod) {
    switch(authMethod) {
        case 'deploy_key':
            return `
# GitHub Deploy Key Setup
1. On your server, run:
   ssh-keygen -t ed25519 -C "deploy@your-domain.com"

2. Copy the public key:
   cat ~/.ssh/id_ed25519.pub

3. Go to your GitHub repository:
   Settings > Deploy Keys > Add deploy key
   - Paste the public key
   - Enable "Allow write access"

4. Add these secrets to your GitHub repository:
   SERVER_HOST: Your server's IP/domain
   SERVER_USERNAME: Your server username
   SSH_PRIVATE_KEY: Content of ~/.ssh/id_ed25519
`;

        case 'pat':
            return `
# Personal Access Token Setup
1. Go to GitHub:
   Settings > Developer settings > Personal access tokens
   - Generate new token
   - Select repo scope

2. Add these secrets to your GitHub repository:
   SERVER_HOST: Your server's IP/domain
   SERVER_USERNAME: Your server username
   GITHUB_TOKEN: Your personal access token
`;
    }
} 