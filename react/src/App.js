import React from 'react';
import './App.css';
import Menu from "./Component/Menu/Menu";
import SignIn from './Component/SignIn/SignIn';
import Copyright from "./Component/Copyright/Copyright";
import Box from "@material-ui/core/Box";
import {makeStyles} from "@material-ui/core/styles";
import CssBaseline from "@material-ui/core/CssBaseline";
import Container from "@material-ui/core/Container";

const useStyles = makeStyles((theme) => ({
    root: {
        display: 'flex',
    },
    content: {
        flexGrow: 1,
        height: '100vh',
        overflow: 'auto',
    },
    container: {
        paddingTop: theme.spacing(4),
        paddingBottom: theme.spacing(4),
    },
}));

function App() {
    const classes = useStyles();

    return (
        <div className={classes.root}>
            <CssBaseline>
                <Menu/>
                <main className={classes.content}>
                    <Container maxWidth="lg" className={classes.container}>
                        <SignIn/>
                    </Container>
                    <Box mt={8}>
                        <Copyright/>
                    </Box>
                </main>
            </CssBaseline>
        </div>
    );
}

export default App;
