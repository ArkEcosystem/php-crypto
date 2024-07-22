const { secp256k1 } = require("bcrypto");

// Function to sign a message using the provided private key
const signMessage = async (privateKeyHex, publicKeyHex, messageHex) => {
    const privateKey = Buffer.from(privateKeyHex, "hex");
    const publicKey = Buffer.from(publicKeyHex, "hex");
    const message = Buffer.from(messageHex, "hex");

    try {
        const signature = await secp256k1.schnorrSign(message, privateKey);
        const signatureHex = signature.toString("hex");
        return {
            status: "success",
            signature: signatureHex,
        };
    } catch (error) {
        return {
            status: "error",
            message: error.message,
        };
    }
};

// Function to parse command line arguments and call signMessage
const main = async () => {
    const args = process.argv.slice(2);
    if (args.length !== 3) {
        console.error(
            JSON.stringify({
                status: "error",
                message:
                    "Usage: node schnorr-signer.js <privateKeyHex> <publicKeyHex> <messageHex>",
            })
        );
        process.exit(1);
    }

    const [privateKeyHex, publicKeyHex, messageHex] = args;
    const result = await signMessage(privateKeyHex, publicKeyHex, messageHex);
    console.log(JSON.stringify(result));
};

main();
