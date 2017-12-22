import PebbleTokenContract from 'contracts/Pebbles.json';
import CONFIG from 'root/config';

class PebbleManager {
    constructor() {
        this.instance = false;
    }

    getInstance() {
        if (!this.instance) {
            this.instance = new BlockchainManager.web3.eth.Contract(PebbleTokenContract.abi, CONFIG.pblContract);
        }

        return this.instance;
    }

    static createInstance() {
        const PebbleToken = new BlockchainManager.web3.eth.Contract(PebbleTokenContract.abi);

        return PebbleToken.deploy({
            data: PebbleTokenContract.bytecode,
            arguments: [CONFIG.deployer]
        })
            .send({
                from: CONFIG.deployer,
                gas: CONFIG.gas
            });
    }

    approve(spender, payer, value) {
        return this.getInstance().methods.approve(spender, value).send({
            from: payer
        });
    }

    balanceOf(address, callback) {
        return this.getInstance().methods.balanceOf(address).call(callback);
    }

    // Method used for development, will increase receivers PBL balance by 1000.
    receivePbls(receiver) {
        return this.getInstance().methods.receivePbls(receiver.wallet_address).send({
            from: receiver.wallet_address
        });
    }
}

export default PebbleManager;
