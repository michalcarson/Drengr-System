import React from "react";
import Container from "@material-ui/core/Container";
import { makeStyles } from "@material-ui/core/styles";
import Member from "../Member/Member";

const useStyles = makeStyles((theme) => ({
    paper: {
        marginTop: theme.spacing(8),
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
    },
}));

const Home = (props) => {
    const classes = useStyles();
    return (
        <Container component="main" maxWidth="xs">
            <div className={classes.paper}>
                <Member member={props.member} />
            </div>
        </Container>
    );
};

export default Home;
