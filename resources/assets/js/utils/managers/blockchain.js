import Web3 from 'web3';

class BlockchainManager {
    constructor(provider) {
        this.web3 = new Web3();
        this.web3.setProvider(provider);
    }

    callAuthorized(manager, method, wallet_address, wallet_password, callback, ...args) {
        this.unlockAccount(wallet_address, wallet_password)
            .then((isUnlocked) => {
                if (!isUnlocked) {
                    callback(new Error('Unable to unlock account'));
                    return;
                }

                manager[method](...args).on('transactionHash', (transactionHash) => {
                    this.lockAccount(wallet_address);
                    return callback(null, transactionHash);
                });
            })
            .catch(error => callback(error, null));
    }

    unlockAccount(wallet_address, wallet_password = '') {
        return this.web3.eth.personal.unlockAccount(wallet_address, wallet_password);
    }

    lockAccount(wallet_address) {
        return this.web3.eth.personal.lockAccount(wallet_address);
    }

    createWallet(password, callback) {
        return this.web3.eth.personal.newAccount(password, callback);
    }

    getTransactionReceipt(transactionHash, callback) {
        return this.web3.eth.getTransactionReceipt(transactionHash, callback);
    }
}

export default BlockchainManager;
