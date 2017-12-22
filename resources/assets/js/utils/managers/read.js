import CONFIG from 'root/config';
import ReadTokenContract from 'contracts/ReadToken.json';

class ReadManager {
    constructor(address) {
        this.address = address;
        this.instance = false;

        if (this.address) {
            this.getInstance();
        }
    }

    getInstance() {
        if (!this.instance) {
            this.instance = new BlockchainManager.web3.eth.Contract(ReadTokenContract.abi, this.address);
        }

        return this.instance;
    }

    static createInstance(owner, author, description, title, price, totalTokens, saleEndDate) {
        const args = arguments;
        const ReadToken = new BlockchainManager.web3.eth.Contract(ReadTokenContract.abi);

        return ReadToken.deploy({
            data: ReadTokenContract.bytecode,
            arguments: args
        })
            .send({
                from: CONFIG.deployer,
                gas: CONFIG.gas
            });
    }

    buy(payer) {
        return this.getInstance().methods.buy().send({
            from: payer
        });
    }
}

export default ReadManager;
