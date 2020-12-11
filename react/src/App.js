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

const currentUser = {
    "id": "1",
    "name": "Michal Carson",
    "viking_name": "Aegil",
    "address": "609 NW 17th Street",
    "city": "Oklahoma City",
    "state": "OK",
    "zip": "73103",
    "date_joined": "2004-01-01",
    "face_picture": null,
    "full_picture": null,
    "created_at": "2020-10-28 03:51:36",
    "updated_at": "2020-10-28 03:51:36",
    "deleted_at": null,
    "wp_user_id": "8",
    "wp_user_validated": "0",
    "certifications": [
        {
            "id": "1",
            "date_achieved": "2004-02-01",
            "certification_id": "1",
            "member_id": "1",
            "assessment_officer_id": "1",
            "extra_points": "0",
            "created_at": "2020-10-28 03:55:22",
            "updated_at": "2020-10-28 03:55:22",
            "deleted_at": null,
            "name": "Basic Combat"
        }
    ],
    "email": [
        {
            "id": "1",
            "email_type_id": "1",
            "member_id": "1",
            "email_address": "michal.carson@gmail.com",
            "created_at": "2020-10-28 20:51:50",
            "updated_at": "2020-10-28 20:51:50",
            "deleted_at": null,
            "type": "Home"
        }
    ],
    "phone": [
        {
            "id": "1",
            "phone_type_id": "1",
            "member_id": "1",
            "phone_number": "4056428857",
            "created_at": "2020-10-28 20:55:19",
            "updated_at": "2020-10-28 20:55:19",
            "deleted_at": null,
            "type": "Home"
        }
    ],
    "rank": [
        {
            "id": "1",
            "member_id": "1",
            "rank_id": "3",
            "date_achieved": "2007-05-01",
            "created_at": "2020-10-28 21:06:11",
            "updated_at": "2020-10-28 21:06:11",
            "deleted_at": null,
            "rank": "Drengr"
        },
        {
            "id": "2",
            "member_id": "1",
            "rank_id": "4",
            "date_achieved": "2014-10-01",
            "created_at": "2020-10-28 21:06:11",
            "updated_at": "2020-10-28 21:06:11",
            "deleted_at": null,
            "rank": "Jarl"
        }
    ]
};

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
                                    <Home member={currentUser}/>
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
