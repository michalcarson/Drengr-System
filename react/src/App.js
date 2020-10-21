import Box from "@material-ui/core/Box";
import Container from "@material-ui/core/Container";
import Copyright from "./Component/Copyright/Copyright";
import CssBaseline from "@material-ui/core/CssBaseline";
import {HashRouter, Route, Switch} from "react-router-dom";
import Home from "./Component/Home/Home";
import {makeStyles} from "@material-ui/core/styles";
import Menu from "./Component/Menu/Menu";
import React from 'react';
import SignIn from './Component/SignIn/SignIn';

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

export default function App() {
    const classes = useStyles();

    return (
        <div className={classes.root}>
            <CssBaseline>
                <HashRouter>
                    <Menu/>
                    <main className={classes.content}>
                        <Container maxWidth="lg" className={classes.container}>
                            <Switch>
                                <Route path={"/signin"}>
                                    <SignIn/>
                                </Route>
                                <Route path="/">
                                    <Home/>
                                </Route>
                            </Switch>
                        </Container>
                        <Box mt={8}>
                            <Copyright/>
                        </Box>
                    </main>
                </HashRouter>
            </CssBaseline>
        </div>
    );
}
